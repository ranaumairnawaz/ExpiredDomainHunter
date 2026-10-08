<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();            // e.g. "vanremoval.co.uk"
            $table->string('tld', 20)->index();          // e.g. "co.uk"
            $table->unsignedSmallInteger('length');      // character count of domain name (no TLD)
            $table->boolean('has_hyphen')->default(false);
            $table->boolean('has_numbers')->default(false);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete()->index();

            // SEO Metrics (filled by queued job)
            $table->unsignedTinyInteger('da')->nullable(); // Moz Domain Authority 0-100
            $table->unsignedTinyInteger('spam_score')->nullable(); // Moz Spam Score 0-17
            $table->unsignedInteger('backlinks')->nullable();

            // Ingestion metadata
            $table->date('dropped_on')->nullable()->index();        // date the domain appeared on drop list
            $table->enum('metrics_status', ['pending', 'fetched', 'failed'])->default('pending')->index();
            $table->timestamps();

            $table->index(['da', 'spam_score']);
            $table->index(['tld', 'length']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
