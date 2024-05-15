<?php

namespace BadChoice\Reports\Filters;

use Illuminate\Support\Arr;

class Filters
{
    public static $singleton;

    protected $filters = [];

    /**
     * Extra filters that won't be saved into session
     * @var array
     */
    protected $extraFilters = [];
    protected $allFilters   = null;

    protected $filtersToKeepInSession = [
        "start_date",
        "end_date",
    ];

    public static function all()
    {
        if (! static::$singleton) {
            static::$singleton = new Filters;
        }
        return static::$singleton->get();
    }

    public static function find($key)
    {
        return static::all()[$key] ?? null;
    }

    public function findFiltersWithSession()
    {
        $request        = request();
        $sessionFilters = session('filters') ?? [];
        $filters        = array_merge($sessionFilters, $request->all());
        session(['filters' => Arr::only($filters, $this->filtersToKeepInSession)]);
        $this->allFilters = array_merge($filters, $this->extraFilters);
    }

    public function add($filter, $value)
    {
        $this->extraFilters[$filter] = $value;
        return $this;
    }

    public function get()
    {
        $this->findFiltersWithSession();
        return $this->clearEmptyFilters($this->allFilters);
    }

    public function clearEmptyFilters($filters) : array{
        return collect($filters)->mapWithKeys(function($value, $key){
            if ($value == null) { return [];}
            if (is_array($value) && count(array_filter($value)) == 0) {
                return [];
            }
            return [$key => $value];
        })->all();
    }

    public function clear()
    {
        session()->forget('filters');
        return $this;
    }
}
