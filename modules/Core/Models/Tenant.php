<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Tenant Model
 * 
 * Represents a tenant in the multi-tenant system. Each tenant has its own
 * isolated database and can have multiple organizations, users, and settings.
 * 
 * @property string $id UUID primary key
 * @property string $domain Primary domain for tenant identification
 * @property string|null $subdomain Subdomain for tenant identification
 * @property string $company_name Company/Tenant name
 * @property string $database_name Name of the tenant's dedicated database
 * @property string $database_host Database host
 * @property int $database_port Database port
 * @property string $status Tenant status (active, suspended, trial, expired)
 * @property string $plan Subscription plan (basic, professional, enterprise)
 * @property int $max_users Maximum allowed users
 * @property int $max_organizations Maximum allowed organizations
 * @property \DateTime|null $subscription_start Subscription start date
 * @property \DateTime|null $subscription_end Subscription end date
 * @property string $billing_email Billing contact email
 * @property array|null $custom_settings Custom tenant settings
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \DateTime|null $deleted_at
 */
final class Tenant extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'domain',
        'subdomain',
        'company_name',
        'database_name',
        'database_host',
        'database_port',
        'status',
        'plan',
        'max_users',
        'max_organizations',
        'subscription_start',
        'subscription_end',
        'billing_email',
        'custom_settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subscription_start' => 'datetime',
        'subscription_end' => 'datetime',
        'custom_settings' => 'array',
        'max_users' => 'integer',
        'max_organizations' => 'integer',
        'database_port' => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'database_name',
        'database_host',
        'database_port',
    ];

    /**
     * Get the organizations for this tenant.
     */
    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    /**
     * Get the users for this tenant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if tenant is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if tenant is on trial.
     */
    public function isTrial(): bool
    {
        return $this->status === 'trial';
    }

    /**
     * Check if tenant subscription has expired.
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->subscription_end && $this->subscription_end->isPast());
    }

    /**
     * Check if tenant is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if tenant can add more users.
     */
    public function canAddUsers(): bool
    {
        return $this->users()->count() < $this->max_users;
    }

    /**
     * Check if tenant can add more organizations.
     */
    public function canAddOrganizations(): bool
    {
        return $this->organizations()->count() < $this->max_organizations;
    }

    /**
     * Get the database connection configuration for this tenant.
     */
    public function getDatabaseConfig(): array
    {
        return [
            'driver' => 'pgsql',
            'host' => $this->database_host,
            'port' => $this->database_port,
            'database' => $this->database_name,
            'username' => config('database.connections.pgsql.username'),
            'password' => config('database.connections.pgsql.password'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ];
    }

    /**
     * Activate the tenant.
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Suspend the tenant.
     */
    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    /**
     * Mark tenant as expired.
     */
    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Tenant $tenant) {
            if (empty($tenant->database_name)) {
                $tenant->database_name = 'tenant_' . uniqid();
            }
            if (empty($tenant->database_host)) {
                $tenant->database_host = config('database.connections.pgsql.host', '127.0.0.1');
            }
            if (empty($tenant->database_port)) {
                $tenant->database_port = config('database.connections.pgsql.port', 5432);
            }
        });
    }
}
