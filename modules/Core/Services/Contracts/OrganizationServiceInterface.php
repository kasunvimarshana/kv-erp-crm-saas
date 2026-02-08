<?php

declare(strict_types=1);

namespace Modules\Core\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Models\Organization;

/**
 * Organization Service Interface
 * 
 * Defines business operations for organization management.
 */
interface OrganizationServiceInterface extends ServiceInterface
{
    /**
     * Get organizations by tenant.
     * 
     * @param string $tenantId
     * @return Collection
     */
    public function getByTenant(string $tenantId): Collection;

    /**
     * Get root organizations.
     * 
     * @return Collection
     */
    public function getRootOrganizations(): Collection;

    /**
     * Get child organizations.
     * 
     * @param string $parentId
     * @return Collection
     */
    public function getChildren(string $parentId): Collection;

    /**
     * Get active organizations.
     * 
     * @return Collection
     */
    public function getActiveOrganizations(): Collection;

    /**
     * Find organization by code.
     * 
     * @param string $code
     * @return Organization|null
     */
    public function findByCode(string $code): ?Organization;
}
