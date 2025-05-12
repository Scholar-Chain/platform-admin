<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class ResourceHelpers
{

    public static function getModelName($modelName, $snakeCase = false): String
    {
        $className = explode('\\', get_class($modelName));
        $className = $className[count($className) - 1];
        if ($snakeCase) {
            return Str::snake($className, '-');
        } else {
            return $className;
        }
    }
}
