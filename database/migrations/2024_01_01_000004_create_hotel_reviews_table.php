<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('ota_source_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reviewer_name');
            $table->integer('rating')->comment('Rating 1-5');
            $table->text('review_text');
            $table->date('review_date');
            $table->string('review_url')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            
            $table->index(['hotel_id', 'ota_source_id']);
            $table->index(['hotel_id', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_reviews');
    }
};
