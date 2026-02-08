<?php

declare(strict_types=1);

namespace Modules\Core\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Models\Tenant;
use Modules\Core\Repositories\Contracts\TenantRepositoryInterface;

/**
 * Tenant Repository
 * 
 * Handles all database operations related to tenants.
 */
final class TenantRepository extends BaseRepository implements TenantRepositoryInterface
{
    /**
     * TenantRepository constructor.
     * 
     * @param Tenant $model
     */
    public function __construct(Tenant $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritDoc}
     */
    public function findByDomain(string $domain): ?Tenant
    {
        return $this->model->where('domain', $domain)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function findBySubdomain(string $subdomain): ?Tenant
    {
        return $this->model->where('subdomain', $subdomain)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function getActiveTenants(): Collection
    {
        return $this->model->where('status', 'active')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getExpiredTenants(): Collection
    {
        return $this->model
            ->where('status', 'expired')
            ->orWhere(function ($query) {
                $query->where('subscription_end', '<', now())
                    ->whereNotNull('subscription_end');
            })
            ->get();
    }
}
