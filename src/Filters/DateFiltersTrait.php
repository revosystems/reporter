<?php

namespace BadChoice\Reports\Filters;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait DateFiltersTrait
{
    public function date($date = null)
    {
        if (! $this->dateField) {
            return $this->builder;
        }
        $date = $date ? : Carbon::today()->toDateString();
        $dt  = Carbon::parse($date . " " . $this->openingTime)->subHours($this->offsetHours);
        $dt2 = Carbon::parse($date . " " . $this->openingTime)->subHours($this->offsetHours)->addDay();
        return $this->builder->whereBetween($this->dateField, [$dt, $dt2]);
    }

    public function start_date($date = null)
    {
        if (! $this->dateField || isset($this->filters()['date'])) {
            return $this->builder;
        }

        $timezone = auth()->user()->timezone;
        return $this->builder->where(DB::raw("DATE(SUBTIME(CONVERT_TZ({$this->rawDateField()}, 'UTC', '{$timezone}'), '{$this->openingTime}'))"), ">=", $date);
    }

    public function end_date($date = null)
    {
        if (! $this->dateField || isset($this->filters()['date'])) {
            return $this->builder;
        }

        $timezone = auth()->user()->timezone;
        return $this->builder->where(DB::raw("DATE(SUBTIME(CONVERT_TZ({$this->rawDateField()}, 'UTC', '{$timezone}'), '{$this->openingTime}'))"), "<=",  $date);
    }

    public function dayOfWeek($weekdays = null)
    {
        $validWeekDays = $this->validWeekdays($weekdays);
        if (! $this->dateField || ! $validWeekDays) {
            return $this->builder;
        }
        return $this->where(DB::raw("dayofweek(" . $this->rawDateField() . ")"), $validWeekDays);
    }

    public function validWeekdays($weekdays)
    {
        return collect($weekdays)->reject(null)->filter(function ($weekday) {
            return $weekday > 0 && $weekday < 8;
        });
    }

    public function start_time($time = null)
    {   
        $timezone = auth()->user()->timezone;

        if (! $this->dateField || ! $time) {
            return $this->builder;
        }
        return $this->builder->where(DB::raw("CONVERT_TZ({$this->rawDateField()}, 'UTC', '{$timezone}')"), ">", DB::raw("CONCAT(DATE(CONVERT_TZ({$this->rawDateField()}, 'UTC', '{$timezone}')), ' {$time}')"));
    }

    public function end_time($time = null)
    {
        $timezone = auth()->user()->timezone;

        if (! $this->dateField || ! $time) {
            return $this->builder;
        }
        return $this->builder->where(DB::raw("CONVERT_TZ({$this->rawDateField()}, 'UTC', '{$timezone}')"), '<', DB::raw("CONCAT(DATE(CONVERT_TZ({$this->rawDateField()}, 'UTC', '{$timezone}')), ' {$time}')"));
    }
}
