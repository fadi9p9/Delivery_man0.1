<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'firstName',
        'lastName',
        'email',
        'password',
        'phoneNumber',
        'role',
        'location',
        'img',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     // Relationships
     public function carts()
     {
         return $this->hasMany(Cart::class, 'userId');
     }
 
     public function orders()
     {
         return $this->hasMany(Order::class, 'customerId');
     }
 
     public function deliveries()
     {
         return $this->hasMany(Order::class, 'deliveryId');
     }
 
     public function favorites()
     {
         return $this->hasMany(Favorite::class, 'userId');
     }
 
     public function markets()
     {
         return $this->hasMany(Market::class, 'userId');
     }
 



}
