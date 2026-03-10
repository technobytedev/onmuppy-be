<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id', 'day_of_week',
        'open_time', 'close_time', 'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}