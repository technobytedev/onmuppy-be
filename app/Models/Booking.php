<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'vendor_id', 'service_id', 'status',
        'location_type', 'address', 'latitude', 'longitude',
        'scheduled_at', 'duration_minutes', 'agreed_price',
        'customer_notes', 'cancellation_reason',
        'confirmed_at', 'completed_at',
    ];

    protected $casts = [
        'latitude'     => 'decimal:7',
        'longitude'    => 'decimal:7',
        'agreed_price' => 'decimal:2',
        'scheduled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function vendor()  { return $this->belongsTo(Vendor::class); }
    public function service() { return $this->belongsTo(Service::class); }

    public function scopePending($query)   { return $query->where('status', 'pending'); }
    public function scopeUpcoming($query)  { return $query->where('scheduled_at', '>', now()); }
    public function scopeCompleted($query) { return $query->where('status', 'completed'); }

    public function isUpcoming(): bool
    {
        return $this->scheduled_at->isFuture()
            && !in_array($this->status, ['cancelled', 'completed']);
    }
}