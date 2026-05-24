<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/*
|--------------------------------------------------------------------------
| Unit VI — User Eloquent Model
|--------------------------------------------------------------------------
*/

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',   // founder | investor | acquirer | advisor
        'avatar_path',
        'bio',
        'company_name',
        'phone',
        'kyc_status',
        'kyc_document',
    ];

    protected $hidden = ['password'];

    protected $casts = ['created_at' => 'datetime'];

    // Unit VI — A user (founder) has many startups
    public function startups()
    {
        return $this->hasMany(Startup::class);
    }

    // Unit VI — A user (investor) has many inquiries
    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
