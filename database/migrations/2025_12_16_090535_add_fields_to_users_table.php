<?php

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



