<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('ota_source_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->string('room_type')->nullable();
            $table->string('booking_url')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamp('last_updated');
            $table->timestamps();
            
            $table->unique(['hotel_id', 'ota_source_id', 'check_in_date', 'check_out_date', 'room_type'], 'unique_hotel_price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_prices');
    }
};
