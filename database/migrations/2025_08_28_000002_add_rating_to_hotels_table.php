<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('hotels', 'rating')) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->decimal('rating', 3, 1)->nullable()->after('location');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('hotels', 'rating')) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->dropColumn('rating');
            });
        }
    }
};
