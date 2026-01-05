<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ==================== BOOT METHOD ====================

    /**
     * Method boot() dipanggil saat model di-initialize.
     * Kita gunakan untuk auto-generate slug.
     */
    protected static function boot()
    {
        parent::boot();

        // Event: creating (Sebelum data disimpan ke DB)
        // Kita gunakan untuk auto-generate slug dari name.
        static::creating(function ($category) {
            if (empty($category->slug)) {
                // Contoh: "Elektronik & Gadget" -> "elektronik-gadget"
                $category->slug = Str::slug($category->name);
            }
        });

        // Event: updating (Sebelum data yang diedit disimpan)
        // Cek jika nama berubah, update juga slug-nya.
        static::updating(function ($category) {
            if ($category->isDirty('name')) { // isDirty() = apakah nilai berubah?
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Kategori memiliki banyak produk.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Hanya produk aktif dan tersedia.
     */
    public function activeProducts()
    {
        return $this->hasMany(Product::class)
                    ->where('is_active', true)
                    ->where('stock', '>', 0);
    }

    // ==================== SCOPES ====================

    /**
     * Scope untuk filter kategori aktif.
     * Penggunaan: Category::active()->get()
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ==================== ACCESSORS ====================

    /**
     * Hitung jumlah produk aktif dalam kategori.
     * Penggunaan: $category->product_count
     */
    public function getProductCountAttribute(): int
    {
        return $this->activeProducts()->count();
    }

    /**
     * URL gambar kategori atau placeholder.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/category-placeholder.png');
    }



    public function getProductsCountAttribute(): int
    {
        // Tips: Untuk performa, sebaiknya gunakan withCount() di controller
        // daripada menghitung manual di sini jika datanya banyak.
        return $this->activeProducts()->count();
    }

    // ==================== BOOT (MODEL EVENTS) ====================


}

