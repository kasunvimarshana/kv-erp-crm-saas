<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Modules\Core\Models\Organization;
use Modules\Core\Repositories\Contracts\OrganizationRepositoryInterface;
use Modules\Core\Services\Contracts\OrganizationServiceInterface;

/**
 * Organization Service
 * 
 * Handles all business logic related to organization management including
 * hierarchical structure, multi-location, and multi-branch operations.
 */
final class OrganizationService implements OrganizationServiceInterface
{
    /**
     * @var OrganizationRepositoryInterface
     */
    private OrganizationRepositoryInterface $repository;

    /**
     * OrganizationService constructor.
     * 
     * @param OrganizationRepositoryInterface $repository
     */
    public function __construct(OrganizationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function getAll(array $filters = []): Collection
    {
        if (isset($filters['tenant_id'])) {
            return $this->repository->getByTenant($filters['tenant_id']);
        }

        if (isset($filters['status']) && $filters['status'] === 'active') {
            return $this->repository->getActiveOrganizations();
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
    public function findByCode(string $code): ?Organization
    {
        return $this->repository->findByCode($code);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Model
    {
        // Validate parent organization if specified
        if (isset($data['parent_id'])) {
            $parent = $this->repository->find($data['parent_id']);
            if (!$parent) {
                throw new \InvalidArgumentException('Parent organization not found');
            }
        }

        $organization = $this->repository->create($data);

        Log::info("Organization created successfully", [
            'organization_id' => $organization->id,
            'name' => $organization->name,
            'tenant_id' => $organization->tenant_id,
        ]);

        return $organization;
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $data): Model
    {
        // Prevent circular references in hierarchy
        if (isset($data['parent_id']) && $data['parent_id'] === $id) {
            throw new \InvalidArgumentException('Organization cannot be its own parent');
        }

        $this->repository->update($id, $data);
        $organization = $this->repository->findOrFail($id);

        Log::info("Organization updated successfully", [
            'organization_id' => $id,
        ]);

        return $organization;
    }

    /**
     * {@inheritDoc}
     */
    public function delete($id): bool
    {
        // Check if organization has children
        $children = $this->repository->getChildren($id);
        if ($children->isNotEmpty()) {
            throw new \RuntimeException('Cannot delete organization with child organizations');
        }

        return $this->repository->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getByTenant(string $tenantId): Collection
    {
        return $this->repository->getByTenant($tenantId);
    }

    /**
     * {@inheritDoc}
     */
    public function getRootOrganizations(): Collection
    {
        return $this->repository->getRootOrganizations();
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren(string $parentId): Collection
    {
        return $this->repository->getChildren($parentId);
    }

    /**
     * {@inheritDoc}
     */
    public function getActiveOrganizations(): Collection
    {
        return $this->repository->getActiveOrganizations();
    }
}
