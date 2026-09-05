<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payee_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('NGN');
            $table->enum('status', ['pending', 'funded', 'released', 'refunded'])->default('pending');
            $table->string('reference')->unique();
            $table->dateTime('funded_at')->nullable();
            $table->dateTime('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('payments'); }
};