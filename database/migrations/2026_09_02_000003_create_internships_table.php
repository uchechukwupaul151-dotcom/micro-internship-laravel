<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->json('required_skills');
            $table->enum('location_type', ['remote', 'hybrid', 'on-site']);
            $table->unsignedTinyInteger('duration_days');
            $table->decimal('stipend', 12, 2)->default(0);
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internships');
    }
};