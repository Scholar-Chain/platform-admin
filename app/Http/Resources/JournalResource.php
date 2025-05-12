<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\MediaFile as MediaHelpers;
use Illuminate\Http\Resources\Json\JsonResource;

class JournalResource extends JsonResource
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

        $result['publish_months'] = collect($result['publish_months'])
            ->map(fn($m) => [
                '1' => 'Januari',
                '2' => 'Februari',
                '3' => 'Maret',
                '4' => 'Aprril',
                '5' => 'Mei',
                '6' => 'Juni',
                '7' => 'Juli',
                '8' => 'Agustus',
                '9' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember'
            ][$m]);
        $result['publisher'] = $this->whenLoaded('publisher');
        $result['thumbnail_url'] = Storage::disk('public')->url($result['thumbnail']);
        unset($result['thumbnail']);
        unset($result['is_active']);
        unset($result['already_edit']);
        unset($result['external_id']);

        return $result;
    }
}
