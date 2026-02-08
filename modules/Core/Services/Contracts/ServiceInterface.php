<?php

declare(strict_types=1);

namespace Modules\Core\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Base Service Interface
 * 
 * Defines the contract for all service implementations.
 * Services encapsulate business logic and orchestrate repository operations.
 */
interface ServiceInterface
{
    /**
     * Get all records.
     * 
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function getAll(array $filters = []): Collection;

    /**
     * Get paginated records.
     * 
     * @param int $perPage
     * @param array<string, mixed> $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15, array $filters = []);

    /**
     * Find a record by ID.
     * 
     * @param string|int $id
     * @return Model|null
     */
    public function findById($id): ?Model;

    /**
     * Create a new record.
     * 
     * @param array<string, mixed> $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update a record.
     * 
     * @param string|int $id
     * @param array<string, mixed> $data
     * @return Model
     */
    public function update($id, array $data): Model;

    /**
     * Delete a record.
     * 
     * @param string|int $id
     * @return bool
     */
    public function delete($id): bool;
}
