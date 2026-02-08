<?php

declare(strict_types=1);

namespace Modules\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Tenant Resource
 * 
 * Transforms Tenant model data for API responses.
 * Hides sensitive information like database credentials.
 */
final class TenantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'domain' => $this->domain,
            'subdomain' => $this->subdomain,
            'company_name' => $this->company_name,
            'status' => $this->status,
            'plan' => $this->plan,
            'max_users' => $this->max_users,
            'max_organizations' => $this->max_organizations,
            'subscription_start' => $this->subscription_start?->toIso8601String(),
            'subscription_end' => $this->subscription_end?->toIso8601String(),
            'billing_email' => $this->billing_email,
            'custom_settings' => $this->custom_settings,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
