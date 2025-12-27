<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
    ];

    // ==================== RELATIONSHIPS ====================

    public function wishlists()
    {
        // Relasi User ke Product melalui tabel wishlists
        return $this->belongsToMany(Product::class, 'wishlists')
            ->withTimestamps(); // Agar created_at/updated_at di pivot terisi
    }

// Helper untuk cek apakah user sudah wishlist produk tertentu
    public function hasInWishlist(Product $product)
    {
        return $this->wishlists()->where('product_id', $product->id)->exists();
    }
}
