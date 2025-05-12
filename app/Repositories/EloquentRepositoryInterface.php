<?php

namespace App\Repositories;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Interface EloquentRepositoryInterface
 * @package App\Repositories
 */
interface EloquentRepositoryInterface
{
    /**
     * @return String
     */
    public function getModelName(): String;

    /**
     * @param array $attributes
     * @return ResourceCollection
     */
    public function all(array $params = []): ResourceCollection;

    /**
     * @param array $attributes
     * @return JsonResource
     */
    public function store(array $attributes): JsonResource;

    /**
     * @param $id
     * @return JsonResource
     */
    public function find(string $id): ?JsonResource;

    /**
     * @param $id
     * @param $attributes
     * @return JsonResource
     */
    public function update(string $id, array $attributes): JsonResource;

    /**
     * @param $id
     * @param $request
     * @return Void
     */
    public function delete(string $id);
}
