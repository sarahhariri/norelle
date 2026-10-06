<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable('name', 'slug', 'description', 'image', 'is_active', 'order')]

class Category extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
          
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
