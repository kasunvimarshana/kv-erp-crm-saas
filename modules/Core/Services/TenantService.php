<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Models\Tenant;
use Modules\Core\Repositories\Contracts\TenantRepositoryInterface;
use Modules\Core\Services\Contracts\TenantServiceInterface;

/**
 * Tenant Service
 * 
 * Handles all business logic related to tenant management including
 * tenant creation, database provisioning, and lifecycle management.
 */
final class TenantService implements TenantServiceInterface
{
    /**
     * @var TenantRepositoryInterface
     */
    private TenantRepositoryInterface $repository;

    /**
     * TenantService constructor.
     * 
     * @param TenantRepositoryInterface $repository
     */
    public function __construct(TenantRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function getAll(array $filters = []): Collection
    {
        if (isset($filters['status'])) {
            return $this->repository->getByStatus($filters['status']);
        }

        return $this->repository->all();
    }

    /**
     * {@inheritDoc}
     */
    public function getPaginated(int $perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function findById($id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findByDomain(string $domain): ?Tenant
    {
        return $this->repository->findByDomain($domain);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function createWithDatabase(array $data): Tenant
    {
        DB::beginTransaction();

        try {
            // Create tenant record
            $tenant = $this->repository->create($data);

            // Create dedicated database for tenant
            $this->createTenantDatabase($tenant);

            // Run migrations for tenant database
            $this->runTenantMigrations($tenant);

            DB::commit();

            Log::info("Tenant created successfully", [
                'tenant_id' => $tenant->id,
                'domain' => $tenant->domain,
            ]);

            return $tenant;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Failed to create tenant", [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $data): Model
    {
        $this->repository->update($id, $data);
        return $this->repository->findOrFail($id);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    public function activate(string $id): Tenant
    {
        /** @var Tenant $tenant */
        $tenant = $this->repository->findOrFail($id);
        $tenant->activate();

        return $tenant;
    }

    /**
     * {@inheritDoc}
     */
    public function suspend(string $id): Tenant
    {
        /** @var Tenant $tenant */
        $tenant = $this->repository->findOrFail($id);
        $tenant->suspend();

        return $tenant;
    }

    /**
     * {@inheritDoc}
     */
    public function getActiveTenants(): Collection
    {
        return $this->repository->getActiveTenants();
    }

    /**
     * {@inheritDoc}
     */
    public function getExpiredTenants(): Collection
    {
        return $this->repository->getExpiredTenants();
    }

    /**
     * {@inheritDoc}
     */
    public function canAddUsers(string $tenantId): bool
    {
        /** @var Tenant $tenant */
        $tenant = $this->repository->findOrFail($tenantId);
        return $tenant->canAddUsers();
    }

    /**
     * {@inheritDoc}
     */
    public function canAddOrganizations(string $tenantId): bool
    {
        /** @var Tenant $tenant */
        $tenant = $this->repository->findOrFail($tenantId);
        return $tenant->canAddOrganizations();
    }

    /**
     * Create dedicated database for tenant.
     * 
     * @param Tenant $tenant
     * @return void
     */
    private function createTenantDatabase(Tenant $tenant): void
    {
        $databaseName = $tenant->database_name;

        // Validate database name to prevent SQL injection
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $databaseName)) {
            throw new \InvalidArgumentException('Invalid database name format');
        }

        // Create database using safe parameter binding
        $connection = DB::connection()->getPdo();
        $quotedDbName = $connection->quote($databaseName);
        DB::statement("CREATE DATABASE {$quotedDbName}");

        Log::info("Tenant database created", [
            'tenant_id' => $tenant->id,
            'database' => $databaseName,
        ]);
    }

    /**
     * Run migrations for tenant database.
     * 
     * @param Tenant $tenant
     * @return void
     */
    private function runTenantMigrations(Tenant $tenant): void
    {
        // This would typically run migrations in the tenant's database
        // For now, we'll log it. Implementation would use Artisan facade
        // to run migrations with a specific database connection.
        
        Log::info("Tenant migrations would be run here", [
            'tenant_id' => $tenant->id,
            'database' => $tenant->database_name,
        ]);

        // TODO: Implement actual migration execution
        // Artisan::call('migrate', [
        //     '--database' => 'tenant',
        //     '--path' => 'database/migrations/tenant',
        //     '--force' => true,
        // ]);
    }
}
