<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Startup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','tagline','description','industry','stage',
        'arr','asking_price','mrr_growth','logo_path',
        'user_id','status','category_id','website',
        'pitch_deck','reject_reason',
    ];

    protected $casts = [
        'arr'          => 'integer',
        'asking_price' => 'integer',
        'created_at'   => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────
    public function founder()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }



    // ── Scopes ────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOfStage($query, $stage)
    {
        if ($stage && $stage !== 'all') {
            return $query->where('stage', $stage);
        }
        return $query;
    }

    // ── Accessors ─────────────────────────────────────────
    public function getFormattedArrAttribute(): string
    {
        if ($this->arr >= 1_000_000) {
            return '$' . round($this->arr / 1_000_000, 1) . 'M';
        }
        return '$' . round($this->arr / 1_000) . 'K';
    }

    public function getFormattedAskingAttribute(): string
    {
        if ($this->asking_price >= 1_000_000) {
            return '$' . round($this->asking_price / 1_000_000, 1) . 'M';
        }
        return '$' . round($this->asking_price / 1_000) . 'K';
    }

    public function getAvgRatingAttribute(): float
    {
        return round($this->reviews->avg('rating') ?? 0, 1);
    }
}
