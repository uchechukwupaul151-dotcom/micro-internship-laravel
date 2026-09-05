<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->unsignedSmallInteger('capacity')->default(1)->after('duration_days');
        });
    }

    public function down(): void
    {
        Schema::table('internships', fn (Blueprint $table) => $table->dropColumn('capacity'));
    }
};