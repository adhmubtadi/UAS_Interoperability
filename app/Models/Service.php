<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'price',
        'duration_minutes',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'duration_minutes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
