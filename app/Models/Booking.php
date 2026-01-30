<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'barber_id',
        'service_id',
        'booking_date',
        'booking_time',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'booking_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the barber for this booking.
     */
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    /**
     * Get the service for this booking.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
