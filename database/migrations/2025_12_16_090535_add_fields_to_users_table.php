<?php
// database/migrations/xxxx_add_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->enum('role', ['customer', 'admin'])
                  ->default('customer')
                  ->after('password');

            $table->string('avatar')
                  ->nullable()
                  ->after('role');

            $table->string('google_id')
                  ->nullable()
                  ->unique()
                  ->after('avatar');

            $table->string('phone', 20)
                  ->nullable()
                  ->after('google_id');

            $table->text('address')
                  ->nullable()
                  ->after('phone');
        });
    }


    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn(['role', 'avatar', 'google_id', 'phone', 'address']);
        });
    }
};





<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    // create_carts_table.php
Schema::create('carts', function (Blueprint $table) {

            $table->id();


            $table->foreignId('category_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('weight')->default(0)->comment('dalam gram');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->index(['category_id', 'is_active']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
