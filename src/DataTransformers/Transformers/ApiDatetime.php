<?php

namespace BadChoice\Reports\DataTransformers\Transformers;

use BadChoice\Reports\DataTransformers\TransformsValueInterface;
use Carbon\Carbon;

class ApiDatetime implements TransformsValueInterface
{
    public function transform($value)   
    {
        if (! $value) {
            return null;
        }
        return Carbon::parse($value)->toDateTimeString();
    }
}
