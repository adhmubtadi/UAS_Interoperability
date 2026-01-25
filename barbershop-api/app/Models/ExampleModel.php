<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;

/**
 * Contoh Model yang menggunakan UUID
 * 
 * Untuk membuat model baru dengan UUID:
 * 1. Extend dari BaseModel (bukan Model)
 * 2. Model otomatis akan menggunakan UUID sebagai primary key
 * 
 * Contoh:
 * class User extends BaseModel implements AuthenticatableContract, AuthorizableContract
 * {
 *     use Authenticatable, Authorizable, HasFactory;
 *     
 *     protected $fillable = ['name', 'email', 'password'];
 * }
 */
class ExampleModel extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
