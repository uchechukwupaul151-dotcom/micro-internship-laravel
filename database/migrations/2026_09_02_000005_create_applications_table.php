<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->text('cover_note')->nullable();
            $table->decimal('match_score', 5, 2)->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending')->index();
            $table->timestamps();
            $table->unique(['internship_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};