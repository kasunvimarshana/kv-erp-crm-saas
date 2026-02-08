<?php

declare(strict_types=1);

namespace Modules\Core\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Repositories\Contracts\RepositoryInterface;

/**
 * Base Eloquent Repository
 * 
 * Provides common data access methods using Eloquent ORM.
 * All module-specific repositories should extend this class.
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * BaseRepository constructor.
     * 
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->select($columns)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(int $perPage = 15, array $columns = ['*'])
    {
        return $this->model->select($columns)->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function find($id, array $columns = ['*']): ?Model
    {
        return $this->model->select($columns)->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findOrFail($id, array $columns = ['*']): Model
    {
        return $this->model->select($columns)->findOrFail($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(string $field, $value, array $columns = ['*']): Collection
    {
        return $this->model->select($columns)->where($field, '=', $value)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(string $field, $value, array $columns = ['*']): ?Model
    {
        return $this->model->select($columns)->where($field, '=', $value)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $attributes): bool
    {
        $model = $this->findOrFail($id);
        return $model->update($attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($id): bool
    {
        $model = $this->findOrFail($id);
        return $model->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function findWhereIn(string $field, array $values, array $columns = ['*']): Collection
    {
        return $this->model->select($columns)->whereIn($field, $values)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return $this->model->count();
    }
}
