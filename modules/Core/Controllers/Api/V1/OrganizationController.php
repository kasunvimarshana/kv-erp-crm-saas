<?php

declare(strict_types=1);

namespace Modules\Core\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Controllers\Api\BaseApiController;
use Modules\Core\Requests\CreateOrganizationRequest;
use Modules\Core\Resources\OrganizationResource;
use Modules\Core\Services\Contracts\OrganizationServiceInterface;

/**
 * Organization API Controller
 * 
 * Handles HTTP requests for organization management.
 * 
 * @group Organization Management
 */
final class OrganizationController extends BaseApiController
{
    /**
     * @var OrganizationServiceInterface
     */
    private OrganizationServiceInterface $organizationService;

    /**
     * OrganizationController constructor.
     * 
     * @param OrganizationServiceInterface $organizationService
     */
    public function __construct(OrganizationServiceInterface $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * Display a listing of organizations.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $filters = $request->only(['tenant_id', 'status']);

        if ($request->has('paginate') && $request->get('paginate') === 'true') {
            $organizations = $this->organizationService->getPaginated((int) $perPage, $filters);
            return $this->successResponse(OrganizationResource::collection($organizations));
        }

        $organizations = $this->organizationService->getAll($filters);
        return $this->successResponse(OrganizationResource::collection($organizations));
    }

    /**
     * Store a newly created organization.
     * 
     * @param CreateOrganizationRequest $request
     * @return JsonResponse
     */
    public function store(CreateOrganizationRequest $request): JsonResponse
    {
        try {
            $organization = $this->organizationService->create($request->validated());
            return $this->createdResponse(new OrganizationResource($organization));
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create organization: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified organization.
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $organization = $this->organizationService->findById($id);

        if (!$organization) {
            return $this->notFoundResponse('Organization not found');
        }

        return $this->successResponse(new OrganizationResource($organization));
    }

    /**
     * Update the specified organization.
     * 
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'type' => ['sometimes', 'string', 'in:headquarters,branch,subsidiary,division'],
            'status' => ['sometimes', 'string', 'in:active,inactive'],
            'email' => ['sometimes', 'email', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:50'],
        ]);

        try {
            $organization = $this->organizationService->update($id, $request->all());
            return $this->successResponse(new OrganizationResource($organization), 'Organization updated successfully');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update organization: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified organization.
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->organizationService->delete($id);
            return $this->successResponse(null, 'Organization deleted successfully');
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete organization: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get root organizations (no parent).
     * 
     * @return JsonResponse
     */
    public function roots(): JsonResponse
    {
        $organizations = $this->organizationService->getRootOrganizations();
        return $this->successResponse(OrganizationResource::collection($organizations));
    }

    /**
     * Get child organizations.
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function children(string $id): JsonResponse
    {
        $organizations = $this->organizationService->getChildren($id);
        return $this->successResponse(OrganizationResource::collection($organizations));
    }

    /**
     * Get active organizations.
     * 
     * @return JsonResponse
     */
    public function active(): JsonResponse
    {
        $organizations = $this->organizationService->getActiveOrganizations();
        return $this->successResponse(OrganizationResource::collection($organizations));
    }
}
