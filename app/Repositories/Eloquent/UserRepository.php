<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Enums\UserRole;
use App\Http\Resources\BaseResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Helpers\MediaFile as MediaHelpers;
use App\Repositories\Eloquent\BaseRepository;

class UserRepository extends BaseRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

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
            if (isset($attributes['author'])) {
                $author = $attributes['author'];
                unset($attributes['author']);
                $attrs = [];

                foreach (['given_name', 'family_name', 'affiliation'] as $value) {
                    if (isset($author[$value])) {
                        $attr[$value] = $author[$value];
                    }
                }

                if (isset($author['wallet_address']) && is_null($data->author->wallet_address)) {
                    $attrs['wallet_address'] = $author['wallet_address'];
                }

                $data->author()->update($attrs);
            }

            $data->update($fills);
        }

        return new BaseResource($data->refresh());
    }
}
