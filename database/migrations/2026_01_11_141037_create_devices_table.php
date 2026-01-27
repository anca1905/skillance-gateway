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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Tambah constrained biar aman

            // --- TAMBAHKAN KOLOM LABEL DI SINI ---
            $table->string('label'); // <--- INI WAJIB ADA
            // -------------------------------------

            $table->string('nomor_hp')->nullable();
            $table->string('api_token')->unique();
            $table->string('status')->default('disconnected');
            $table->date('expired_date')->nullable();
            $table->integer('quota')->default(1000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
