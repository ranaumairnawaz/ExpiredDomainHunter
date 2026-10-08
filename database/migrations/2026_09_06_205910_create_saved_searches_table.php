<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // human label for this saved search

            // Filter criteria stored as JSON
            $table->json('filters');                     // {tld, min_da, max_spam, max_length, no_hyphen, no_numbers, category_id, keyword}

            $table->boolean('email_alert')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamp('last_alerted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};
