<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = ['species', 'name', 'age', 'description', 'cage_id', 'image'];

    public function cage()
    {
        return $this->belongsTo(Cage::class);
    }
}