<?php

namespace App\Traits;


trait FieldsType
{
    public function getHashFields(): array
    {
        return [];
    }

    public function getImageFields(): array
    {
        return [
            'main_image_path'
        ];
    }

    public function getOrderFields(): array
    {
        return [
            'order'
        ];
    }

    public function getOrderFieldsFilters(): array
    {
        return [];
    }
}
