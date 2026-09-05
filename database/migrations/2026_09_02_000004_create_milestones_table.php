<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sequence');
            $table->string('title');
            $table->text('deliverable');
            $table->text('criteria');
            $table->date('due_date');
            $table->timestamps();
            $table->unique(['internship_id', 'sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};