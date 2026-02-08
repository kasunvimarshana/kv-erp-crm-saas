<?php

declare(strict_types=1);

namespace Modules\Core\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Models\Tenant;

/**
 * Tenant Service Interface
 * 
 * Defines business operations for tenant management.
 */
interface TenantServiceInterface extends ServiceInterface
{
    /**
     * Find tenant by domain.
     * 
     * @param string $domain
     * @return Tenant|null
     */
    public function findByDomain(string $domain): ?Tenant;

    /**
     * Create a new tenant with database.
     * 
     * @param array<string, mixed> $data
     * @return Tenant
     */
    public function createWithDatabase(array $data): Tenant;

    /**
     * Activate a tenant.
     * 
     * @param string $id
     * @return Tenant
     */
    public function activate(string $id): Tenant;

    /**
     * Suspend a tenant.
     * 
     * @param string $id
     * @return Tenant
     */
    public function suspend(string $id): Tenant;

    /**
     * Get active tenants.
     * 
     * @return Collection
     */
    public function getActiveTenants(): Collection;

    /**
     * Get expired tenants.
     * 
     * @return Collection
     */
    public function getExpiredTenants(): Collection;

    /**
     * Check if tenant can add more users.
     * 
     * @param string $tenantId
     * @return bool
     */
    public function canAddUsers(string $tenantId): bool;

    /**
     * Check if tenant can add more organizations.
     * 
     * @param string $tenantId
     * @return bool
     */
    public function canAddOrganizations(string $tenantId): bool;
}
