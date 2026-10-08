<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();            // e.g. "Removal", "Man & Van"
            $table->string('slug')->unique();            // e.g. "removal", "man-van"
            $table->json('keywords');                    // JSON array of trigger keywords
            $table->string('color', 20)->default('#6366f1'); // UI badge color
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
