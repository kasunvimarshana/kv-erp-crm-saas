<?php

declare(strict_types=1);

namespace Modules\Core\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Models\Organization;
use Modules\Core\Repositories\Contracts\OrganizationRepositoryInterface;

/**
 * Organization Repository
 * 
 * Handles all database operations related to organizations.
 */
final class OrganizationRepository extends BaseRepository implements OrganizationRepositoryInterface
{
    /**
     * OrganizationRepository constructor.
     * 
     * @param Organization $model
     */
    public function __construct(Organization $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritDoc}
     */
    public function getByTenant(string $tenantId): Collection
    {
        return $this->model->where('tenant_id', $tenantId)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getRootOrganizations(): Collection
    {
        return $this->model->whereNull('parent_id')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren(string $parentId): Collection
    {
        return $this->model->where('parent_id', $parentId)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getActiveOrganizations(): Collection
    {
        return $this->model->where('status', 'active')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findByCode(string $code): ?Organization
    {
        return $this->model->where('code', $code)->first();
    }
}
