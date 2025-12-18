<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'google_id',
        'phone',
        'address',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function cart()
    {
        return $this->hasOne(Cart::class);
    }


    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }


    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists')
                    ->withTimestamps();
    }


    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }


    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }


    public function hasInWishlist(Product $product): bool
    {
        return $this->wishlists()
                    ->where('product_id', $product->id)
                    ->exists();
    }
}
