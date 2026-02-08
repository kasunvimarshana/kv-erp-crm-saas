<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Organization Model
 * 
 * Represents an organization within a tenant. Supports hierarchical 
 * structure for multi-organization operations.
 * 
 * @property string $id UUID primary key
 * @property string $tenant_id Foreign key to tenant
 * @property string|null $parent_id Parent organization (for hierarchy)
 * @property string $name Organization name
 * @property string $code Unique organization code
 * @property string|null $description Organization description
 * @property string $type Organization type (headquarters, branch, subsidiary)
 * @property string $status Organization status (active, inactive)
 * @property string|null $email Primary email
 * @property string|null $phone Primary phone
 * @property string|null $address Full address
 * @property string|null $city City
 * @property string|null $state State/Province
 * @property string|null $country Country
 * @property string|null $postal_code Postal/ZIP code
 * @property string $currency Primary currency code (ISO 4217)
 * @property string $timezone Timezone (IANA format)
 * @property string $locale Locale code (ISO 639-1)
 * @property array|null $settings Custom organization settings
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \DateTime|null $deleted_at
 */
final class Organization extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organizations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'tenant_id',
        'parent_id',
        'name',
        'code',
        'description',
        'type',
        'status',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'currency',
        'timezone',
        'locale',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Get the tenant that owns the organization.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the parent organization.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'parent_id');
    }

    /**
     * Get the child organizations.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Organization::class, 'parent_id');
    }

    /**
     * Get the users in this organization.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if organization is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if organization is headquarters.
     */
    public function isHeadquarters(): bool
    {
        return $this->type === 'headquarters';
    }

    /**
     * Check if organization is a branch.
     */
    public function isBranch(): bool
    {
        return $this->type === 'branch';
    }

    /**
     * Check if organization has parent.
     */
    public function hasParent(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Check if organization has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Scope active organizations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope headquarters organizations.
     */
    public function scopeHeadquarters($query)
    {
        return $query->where('type', 'headquarters');
    }

    /**
     * Scope root organizations (no parent).
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Organization $organization) {
            if (empty($organization->code)) {
                $organization->code = strtoupper(substr($organization->name, 0, 3)) . uniqid();
            }
            if (empty($organization->currency)) {
                $organization->currency = 'USD';
            }
            if (empty($organization->timezone)) {
                $organization->timezone = 'UTC';
            }
            if (empty($organization->locale)) {
                $organization->locale = 'en';
            }
        });
    }
}
