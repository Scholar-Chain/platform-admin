<?php

namespace App\Http\Resources;

use App\Enums\UserRole;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\MediaFile as MediaHelpers;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    private function getModelName($modelName, $snakeCase = false): String
    {
        $className = explode('\\', get_class($modelName));
        $className = $className[count($className) - 1];
        if ($snakeCase) {
            return Str::snake($className, '-');
        } else {
            return $className;
        }
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $result = [];
        foreach ($this->attributesToArray() as $attribute => $value) {
            if (in_array($attribute, $this->getImageFields()) && !is_null($value)) {
                $config_name = 'media.image.' . $this->getModelName($this->resource, true) . '.' . $attribute;
                $v = null;
                if (config()->has($config_name)) {
                    $media = new MediaHelpers;
                    $v = !is_null($value) ? $media->getImages($value, config($config_name . '.variants'), true) : null;
                } else {
                    $v = Storage::url($value);
                }
                $result[$attribute] = $v;
            } else {
                $result[$attribute] = $value;
            }
        }

        if (auth('api')->user()->hasRole(UserRole::AUTHOR->value)) {
            $result['author'] = $this->author;
        }

        return $result;
    }
}
