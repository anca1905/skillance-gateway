<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('autoreplies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade'); // Terikat ke device tertentu
            $table->string('keyword'); // Kata kunci (misal: "menu")
            $table->text('response');  // Balasan (misal: "Ada Nasi Goreng, Mie...")
            $table->enum('search_type', ['exact', 'contains'])->default('exact'); // Persis atau Mengandung kata
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autoreplies');
    }
};
