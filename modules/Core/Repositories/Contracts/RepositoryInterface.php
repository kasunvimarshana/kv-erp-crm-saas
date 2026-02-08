<?php

declare(strict_types=1);

namespace Modules\Core\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Base Repository Interface
 * 
 * Defines the contract for all repository implementations following the Repository Pattern.
 * This abstraction allows for flexible data access strategies while keeping business logic clean.
 */
interface RepositoryInterface
{
    /**
     * Get all records.
     * 
     * @param array<string> $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Paginate records.
     * 
     * @param int $perPage
     * @param array<string> $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*']);

    /**
     * Find a record by ID.
     * 
     * @param string|int $id
     * @param array<string> $columns
     * @return Model|null
     */
    public function find($id, array $columns = ['*']): ?Model;

    /**
     * Find a record by ID or fail.
     * 
     * @param string|int $id
     * @param array<string> $columns
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id, array $columns = ['*']): Model;

    /**
     * Find records by a field.
     * 
     * @param string $field
     * @param mixed $value
     * @param array<string> $columns
     * @return Collection
     */
    public function findBy(string $field, $value, array $columns = ['*']): Collection;

    /**
     * Find a single record by a field.
     * 
     * @param string $field
     * @param mixed $value
     * @param array<string> $columns
     * @return Model|null
     */
    public function findOneBy(string $field, $value, array $columns = ['*']): ?Model;

    /**
     * Create a new record.
     * 
     * @param array<string, mixed> $attributes
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     * Update a record.
     * 
     * @param string|int $id
     * @param array<string, mixed> $attributes
     * @return bool
     */
    public function update($id, array $attributes): bool;

    /**
     * Delete a record.
     * 
     * @param string|int $id
     * @return bool
     */
    public function delete($id): bool;

    /**
     * Find records where a field is in an array of values.
     * 
     * @param string $field
     * @param array<mixed> $values
     * @param array<string> $columns
     * @return Collection
     */
    public function findWhereIn(string $field, array $values, array $columns = ['*']): Collection;

    /**
     * Count records.
     * 
     * @return int
     */
    public function count(): int;
}
