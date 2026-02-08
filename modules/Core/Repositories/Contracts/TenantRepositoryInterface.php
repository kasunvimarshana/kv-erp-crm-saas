<?php

declare(strict_types=1);

namespace Modules\Core\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Models\Tenant;

/**
 * Tenant Repository Interface
 * 
 * Defines the contract for tenant data access operations.
 */
interface TenantRepositoryInterface extends RepositoryInterface
{
    /**
     * Find tenant by domain.
     * 
     * @param string $domain
     * @return Tenant|null
     */
    public function findByDomain(string $domain): ?Tenant;

    /**
     * Find tenant by subdomain.
     * 
     * @param string $subdomain
     * @return Tenant|null
     */
    public function findBySubdomain(string $subdomain): ?Tenant;

    /**
     * Get active tenants.
     * 
     * @return Collection
     */
    public function getActiveTenants(): Collection;

    /**
     * Get tenants by status.
     * 
     * @param string $status
     * @return Collection
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get expired tenants.
     * 
     * @return Collection
     */
    public function getExpiredTenants(): Collection;
}
