<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'description'];

    // One category has many startups
    public function startups()
    {
        return $this->hasMany(Startup::class);
    }
}
