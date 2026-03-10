<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id', 'name', 'slug', 'description',
        'price', 'price_type', 'duration_minutes',
        'category', 'image', 'is_available', 'is_home_service',
    ];

    protected $casts = [
        'price'           => 'decimal:2',
        'is_available'    => 'boolean',
        'is_home_service' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}