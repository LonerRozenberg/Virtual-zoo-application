<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cage extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'capacity'];

    public function animals()
    {
        return $this->hasMany(Animal::class);
    }

    public function getOccupancyAttribute()
    {
        return $this->animals()->count();
    }

    public function hasSpace()
    {
        return $this->occupancy < $this->capacity;
    }
}