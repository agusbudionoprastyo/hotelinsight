<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('hotels', 'location')) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->string('location')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('hotels', 'location')) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->dropColumn('location');
            });
        }
    }
};
