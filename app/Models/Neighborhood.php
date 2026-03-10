<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'region',
        'center_lat',
        'center_lng',
        'radius_km',
    ];

    protected $casts = [
        'center_lat' => 'decimal:7',
        'center_lng' => 'decimal:7',
        'radius_km'  => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_neighborhoods')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }

    public function vouches()
    {
        return $this->hasMany(Vouch::class);
    }
}
