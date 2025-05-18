<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Styles extends Model
{
    protected $table = 'styles';
    protected $fillable = [
        'name',
        'description',
        'image',
        'category',
        'tag',
        'status',
    ];

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image);
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }
    public function getUpdatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
