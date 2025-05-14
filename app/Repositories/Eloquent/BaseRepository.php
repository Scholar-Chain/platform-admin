<?php

namespace App\Repositories\Eloquent;

use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use App\Helpers\MediaFile as MediaHelpers;
use Illuminate\Support\Str;
use App\Http\Resources\BaseResource;
use App\Http\Resources\BaseCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseRepository implements EloquentRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

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
     * @return String
     */
    public function getModelName($snakeCase = false): String
    {
        $className = explode('\\', get_class($this->model));
        $className = $className[count($className) - 1];
        if ($snakeCase) {
            return Str::snake($className, '-');
        } else {
            return $className;
        }
    }

    /**
     * @return ResourceCollection
     */
    public function all(array $params = []): ResourceCollection
    {
        $data = $this->model;
        foreach ($this->model->getFillable() as $fillable) {
            if (isset($params[$fillable]) && $fillable !== 'order' && !is_null($params[$fillable]) && $params[$fillable] !== '') {
                $data = $data->where($fillable, 'LIKE', '%' . $params[$fillable] . '%');
            }
        }

        if ($this->model->timestamps) {
            if (isset($params['order']) && in_array($params['order'], $this->model->getFillable())) {
                $data = $data->orderBy($params['order'], isset($params['ascending']) && $params['ascending'] == 0 ? 'DESC' : 'ASC');
            } else {
                $data = $data->orderBy('created_at', 'ASC');
            }
        }

        return new BaseCollection($data->paginate(isset($params['limit']) ? $params['limit'] : 25));
    }

    /**
     * @param array $attributes
     *
     * @return BaseResource
     */
    public function store(array $attributes): BaseResource
    {
        $fills = [];
        foreach ($this->model->getFillable() as $fillable) {
            if (in_array($fillable, $this->model->getImageFields()) && isset($attributes[$fillable]) && !is_null($attributes[$fillable]) && request()->hasFile($fillable)) {
                $config_name = 'media.image.' . $this->getModelName(true) . '.' . $fillable;
                $value = null;
                if (config()->has($config_name)) {
                    $mediaHelpers = new MediaHelpers;
                    $images = $mediaHelpers->generateImages(request()->file($fillable), config($config_name . '.base_directory'), config($config_name . '.variants'));
                    $value = $images[''];
                } else {
                    $value = request()->file($fillable)->store('uploads/images/' . $this->getModelName(true) . '/' . date('Y/m/d'));
                }
                $fills[$fillable] = $value;
            } else if (in_array($fillable, $this->model->getOrderFields())) {
                $m = $this->model;
                foreach ($this->model->getOrderFieldsFilters() as $a) {
                    $m = $m->where($a, $attributes[$a]);
                }
                $fills[$fillable] = $m->count() + 1;
            } else {
                if ($fillable !== 'id' && (isset($attributes[$fillable]) && !is_null($attributes[$fillable]) && trim($attributes[$fillable]) !== '')) {
                    if (in_array($fillable, $this->model->getHashFields())) {
                        $fills[$fillable] = Hash::make($attributes[$fillable]);
                    } else {
                        $fills[$fillable] = $attributes[$fillable];
                    }
                }
            }
        }
        $data = $this->model;
        if (count($fills)) {
            $data = $data->create($fills);
        }
        return new BaseResource($data);
    }

    /**
     * @param $id
     * @return BaseResource
     */
    public function find(string $id): ?JsonResource
    {
        $data = $this->model->find($id);
        if (is_null($data)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException;
        }
        return new BaseResource($data);
    }

    /**
     * @param $id
     * @param $attributes
     * @return BaseResource
     */
    public function update(string $id, array $attributes): BaseResource
    {
        $data = $this->model->find($id);
        $fills = [];
        foreach ($this->model->getFillable() as $fillable) {
            if (in_array($fillable, $this->model->getImageFields()) && isset($attributes[$fillable]) && !is_null($attributes[$fillable]) && request()->hasFile($fillable)) {
                $config_name = 'media.image.' . $this->getModelName(true) . '.' . $fillable;
                $value = null;
                if (config()->has($config_name)) {
                    $mediaHelpers = new MediaHelpers;
                    $mediaHelpers->deleteImages($data->{$fillable}, config($config_name . '.variants'));
                    $images = $mediaHelpers->generateImages(request()->file($fillable), config($config_name . '.base_directory'), config($config_name . '.variants'));
                    $value = $images[''];
                } else {
                    if (!is_null($data->{$fillable})) {
                        Storage::delete($data->{$fillable});
                    }
                    $value = request()->file($fillable)->store('uploads/images/' . $this->getModelName(true) . '/' . date('Y/m/d'));
                }
                $fills[$fillable] = $value;
            } else {
                if ($fillable !== 'id' && (isset($attributes[$fillable]) && !is_null($attributes[$fillable]) && trim($attributes[$fillable]) !== '')) {
                    if (in_array($fillable, $this->model->getHashFields())) {
                        $fills[$fillable] = Hash::make($attributes[$fillable]);
                    } else {
                        $fills[$fillable] = $attributes[$fillable];
                    }
                }
            }
        }
        if (count($fills)) {
            $data->update($fills);
        }
        return new BaseResource($data->refresh());
    }

    /**
     * @param $id
     *
     * @return Void
     */
    public function delete(string $id): Void
    {
        $data = $this->find($id);
        foreach ($this->model->getFillable() as $fillable) {
            if (in_array($fillable, $this->model->getImageFields())) {
                if (!is_null($data->{$fillable})) {
                    $config_name = 'media.image.' . $this->getModelName(true) . '.' . $fillable;
                    if (config()->has($config_name)) {
                        $mediaHelpers = new MediaHelpers;
                        $mediaHelpers->deleteImages($data->{$fillable}, config($config_name . '.variants'));
                    } else {
                        Storage::delete($data->{$fillable});
                    }
                }
            }
        }
        $data->delete();
    }
}
