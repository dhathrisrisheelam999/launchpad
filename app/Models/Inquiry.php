<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/*
|--------------------------------------------------------------------------
| Unit VI — Inquiry Eloquent Model
|--------------------------------------------------------------------------
*/

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'startup_id',
        'user_id',
        'investor_name',
        'organisation',
        'interest_type', // investment | acquisition | acqui-hire | partnership
        'message',
        'status',        // unread | replied | closed
        'reply_message',
        'replied_at',
        'replied_by',
    ];

    // Unit VI — Belongs to a startup
    public function startup()
    {
        return $this->belongsTo(Startup::class);
    }

    // Unit VI — Belongs to an investor user
    public function investor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function repliedBy()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }
}
