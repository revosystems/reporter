<?php

namespace BadChoice\Reports\DataTransformers\Transformers;

use BadChoice\Reports\DataTransformers\TransformsValueInterface;
use Carbon\Carbon;

class DateDay implements TransformsValueInterface
{
    public function transform($value){
        return Carbon::parse($value)->timezone( auth()->user()->timezone )->day;
    }
}