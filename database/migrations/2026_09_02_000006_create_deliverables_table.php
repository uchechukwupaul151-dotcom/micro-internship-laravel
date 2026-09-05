<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliverables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_path')->nullable();
            $table->string('repository_url')->nullable();
            $table->char('sha256_hash', 64)->nullable();
            $table->enum('status', ['unassigned', 'in_progress', 'pending_verification', 'verified', 'rejected'])->default('unassigned')->index();
            $table->text('feedback')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['milestone_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliverables');
    }
};