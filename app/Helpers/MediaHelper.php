<?php

namespace App\Helpers;

use Intervention\Image\ImageManagerStatic;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaFile
{
    protected $image;
    public function __construct()
    {
        $this->image = new ImageManagerStatic;
    }

    public function getImages($ogirinalFilePath, $variants, $asUrl = false)
    {
        $basePath = dirname($ogirinalFilePath);
        $baseFileName = basename($ogirinalFilePath);
        $results = [];
        foreach ($variants as $key => $variant) {
            $results[$key] = $basePath . '/' . ($key !== '' ? $key . '_' : '') . $baseFileName;
            if ($asUrl) {
                $results[$key] = $results[$key];
            }
        }
        return $results;
    }

    public function generateImages($source, $destinations, $variants, $baseFileName = null, $outputExtension = 'jpg')
    {
        if (is_null($baseFileName)) {
            $baseFileName = Str::random(40) . time();
        }
        Storage::makeDirectory($destinations);
        $results = [];
        foreach ($variants as $key => $variant) {
            $img = $this->image::make($source);
            $img->encode($outputExtension);
            if ($variant['fit']) {
                $img->fit($variant['width'], $variant['height']);
            } else {
                $img->resize($variant['width'], $variant['height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            $results[$key] = Storage::url($destinations . '/' . ($key !== '' ? $key . '_' : '') . $baseFileName . '.' . $outputExtension);
            Storage::put($destinations . '/' . ($key !== '' ? $key . '_' : '') . $baseFileName . '.' . $outputExtension,  $img->encode($outputExtension, 80));
        }
        return $results;
    }

    public function deleteImages($ogirinalFilePath, $variants)
    {
        $ogirinalFilePath = str_replace(Storage::url(''), '', $ogirinalFilePath);
        $basePath = dirname($ogirinalFilePath);
        $baseFileName = basename($ogirinalFilePath);
        foreach ($variants as $key => $variant) {
            Storage::delete($basePath . '/' . ($key !== '' ? $key . '_' : '') . $baseFileName);
        }
    }
}
