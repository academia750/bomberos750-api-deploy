<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Opposition extends Model
{
    use HasFactory, SoftDeletes;

    public $keyType = "string";
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'period',
        'is_visible'
    ];

    public array $allowedSorts = [
        'name',
        'period',
        "created-at"
    ];

    public array $adapterSorts = [
        'name' => "Name",
        'period' => "Period",
        "created-at" => "CreatedAt",
    ];

    public array $allowedFilters = [
        "search",
        "day",
        "month",
        "year",
        "date"
    ];

    public array $adapterFilters = [
        "search" => "Search",
        "day" => "Day",
        "month" => "Month",
        "year" => "Year",
        "date" => "Date",
    ];

    public array $allowedIncludes = [];

    public array $adapterIncludes = [];

     protected $casts = [
         'id' => 'string'
     ];

    /* -------------------------------------------------------------------------------------------------------------- */
    // Sorts functions

    public function sortName(Builder $query, $direction): void{
        $query->orderBy('name', $direction);
    }

    public function sortPeriod(Builder $query, $direction): void{
        $query->orderBy('period', $direction);
    }

    public function sortCreatedAt(Builder $query, $direction): void{
        $query->orderBy('created_at', $direction);
    }

    /* -------------------------------------------------------------------------------------------------------------- */
    // Filters functions

    public function filterYear(Builder $query, $value): void{
        $query->whereYear('created_at', $value);
    }
    public function filterMonth(Builder $query, $value): void{
        $query->whereMonth('created_at', $value);
    }
    public function filterDay(Builder $query, $value): void{
        $query->whereDay('created_at', $value);
    }
    public function filterDate(Builder $query, $value): void{
        $query->whereDate('created_at', $value);
    }

    public function filterSearch(Builder $query, $value): void{
        $query->orWhere(function($query) use ($value) {
            $query->where('field', 'LIKE' , "%{$value}%")
                ->orWhere('other_field', 'LIKE' , "%{$value}%");
        });
    }

    /* -------------------------------------------------------------------------------------------------------------- */
     // Relationships methods


}
