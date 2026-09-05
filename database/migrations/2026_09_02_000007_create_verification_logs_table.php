<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deliverable_id')->constrained()->cascadeOnDelete();
            $table->boolean('passed');
            $table->json('checks');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_logs');
    }
};