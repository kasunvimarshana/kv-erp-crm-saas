<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Core\Controllers\Api\V1\OrganizationController;
use Modules\Core\Controllers\Api\V1\TenantController;

/*
|--------------------------------------------------------------------------
| Core Module API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the Core module.
| These routes are loaded by the CoreServiceProvider and all of them
| will be assigned to the "api" middleware group and prefixed with "api".
|
*/

// Central Tenant Management Routes (no tenant context required)
Route::prefix('v1/central')->middleware(['api'])->group(function () {
    // Tenant Management (for super admin)
    Route::apiResource('tenants', TenantController::class);
    Route::post('tenants/{id}/activate', [TenantController::class, 'activate']);
    Route::post('tenants/{id}/suspend', [TenantController::class, 'suspend']);
});

// Tenant-scoped routes (requires tenant identification)
Route::prefix('v1')->middleware(['api', 'tenant'])->group(function () {
    // Organization Management - custom routes first to prevent conflicts
    Route::get('organizations/roots', [OrganizationController::class, 'roots']);
    Route::get('organizations/active', [OrganizationController::class, 'active']);
    Route::get('organizations/{id}/children', [OrganizationController::class, 'children']);
    
    // Organization CRUD routes
    Route::apiResource('organizations', OrganizationController::class);
});
