<?php

namespace App\Repositories\Eloquent;

use App\Models\Submission;
use App\Http\Resources\BaseResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\BaseCollection;
use Illuminate\Support\Facades\Storage;
use App\Helpers\MediaFile as MediaHelpers;
use App\Http\Resources\SubmissionResource;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SubmissionRepository extends BaseRepository
{
    protected $model;

    public function __construct(Submission $model)
    {
        $this->model = $model;
    }

    public function all(array $params = []): ResourceCollection
    {
        $data = $this->model->with([]);
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

        return SubmissionResource::collection($data->paginate(isset($params['limit']) ? $params['limit'] : 25));
    }

    public function find(string $id): ?JsonResource
    {
        $data = $this->model->find($id);
        if (is_null($data)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException;
        }
        return new SubmissionResource($data);
    }
}
