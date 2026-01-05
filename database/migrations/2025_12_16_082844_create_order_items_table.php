<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

// Restrict delete: jangan hapus produk kalau ada di order
            $table->foreignId('product_id')
                ->constrained()
                ->restrictOnDelete();

// Snapshot data produk saat order
// (karena harga/nama produk bisa berubah di kemudian hari)
            $table->string('product_name');
            $table->decimal('price', 12, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained();

    // PENTING: Snapshot data produk saat transaksi
    $table->string('product_name'); // Simpan nama kalau-kalau produk dihapus/diubah
    $table->integer('quantity');
    $table->decimal('price', 12, 2); // Simpan harga SAAT transaksi, bukan relasi ke harga produk sekarang
    $table->decimal('subtotal', 12, 2); // quantity * price

    $table->timestamps();
});
    }
};
