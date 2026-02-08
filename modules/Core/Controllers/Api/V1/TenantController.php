<?php

declare(strict_types=1);

namespace Modules\Core\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Controllers\Api\BaseApiController;
use Modules\Core\Requests\CreateTenantRequest;
use Modules\Core\Resources\TenantResource;
use Modules\Core\Services\Contracts\TenantServiceInterface;

/**
 * Tenant API Controller
 * 
 * Handles HTTP requests for tenant management.
 * 
 * @group Tenant Management
 */
final class TenantController extends BaseApiController
{
    /**
     * @var TenantServiceInterface
     */
    private TenantServiceInterface $tenantService;

    /**
     * TenantController constructor.
     * 
     * @param TenantServiceInterface $tenantService
     */
    public function __construct(TenantServiceInterface $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of tenants.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $filters = $request->only(['status']);

        if ($request->has('paginate') && $request->get('paginate') === 'true') {
            $tenants = $this->tenantService->getPaginated((int) $perPage, $filters);
            return $this->successResponse(TenantResource::collection($tenants));
        }

        $tenants = $this->tenantService->getAll($filters);
        return $this->successResponse(TenantResource::collection($tenants));
    }

    /**
     * Store a newly created tenant.
     * 
     * @param CreateTenantRequest $request
     * @return JsonResponse
     */
    public function store(CreateTenantRequest $request): JsonResponse
    {
        try {
            $tenant = $this->tenantService->create($request->validated());
            return $this->createdResponse(new TenantResource($tenant));
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create tenant: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified tenant.
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $tenant = $this->tenantService->findById($id);

        if (!$tenant) {
            return $this->notFoundResponse('Tenant not found');
        }

        return $this->successResponse(new TenantResource($tenant));
    }

    /**
     * Update the specified tenant.
     * 
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'domain' => ['sometimes', 'string', 'max:255'],
            'company_name' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:active,suspended,trial,expired'],
            'plan' => ['sometimes', 'string', 'in:basic,professional,enterprise'],
            'max_users' => ['sometimes', 'integer', 'min:1'],
            'max_organizations' => ['sometimes', 'integer', 'min:1'],
        ]);

        try {
            $tenant = $this->tenantService->update($id, $request->all());
            return $this->successResponse(new TenantResource($tenant), 'Tenant updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update tenant: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified tenant.
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->tenantService->delete($id);
            return $this->successResponse(null, 'Tenant deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete tenant: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Activate a tenant.
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function activate(string $id): JsonResponse
    {
        try {
            $tenant = $this->tenantService->activate($id);
            return $this->successResponse(new TenantResource($tenant), 'Tenant activated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to activate tenant: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Suspend a tenant.
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function suspend(string $id): JsonResponse
    {
        try {
            $tenant = $this->tenantService->suspend($id);
            return $this->successResponse(new TenantResource($tenant), 'Tenant suspended successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to suspend tenant: ' . $e->getMessage(), 500);
        }
    }
}
