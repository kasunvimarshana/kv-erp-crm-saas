<?php

declare(strict_types=1);

namespace Modules\Core\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Create Tenant Request
 * 
 * Validates data for creating a new tenant.
 */
final class CreateTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: Add proper authorization logic
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'domain' => ['required', 'string', 'max:255', 'unique:tenants,domain'],
            'subdomain' => ['nullable', 'string', 'max:255', 'unique:tenants,subdomain'],
            'company_name' => ['required', 'string', 'max:255'],
            'database_name' => ['nullable', 'string', 'max:255'],
            'database_host' => ['nullable', 'string', 'max:255'],
            'database_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'status' => ['required', 'string', 'in:active,suspended,trial,expired'],
            'plan' => ['required', 'string', 'in:basic,professional,enterprise'],
            'max_users' => ['required', 'integer', 'min:1'],
            'max_organizations' => ['required', 'integer', 'min:1'],
            'subscription_start' => ['nullable', 'date'],
            'subscription_end' => ['nullable', 'date', 'after:subscription_start'],
            'billing_email' => ['required', 'email', 'max:255'],
            'custom_settings' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'domain.required' => 'The domain field is required.',
            'domain.unique' => 'This domain is already taken.',
            'company_name.required' => 'The company name field is required.',
            'billing_email.required' => 'The billing email field is required.',
            'billing_email.email' => 'Please provide a valid email address.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
