<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('github_commits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deliverable_id')->constrained()->cascadeOnDelete();
            $table->string('commit_hash', 64);
            $table->string('author_name');
            $table->string('author_email')->nullable();
            $table->text('message')->nullable();
            $table->longText('diff')->nullable();
            $table->timestamp('committed_at')->nullable();
            $table->timestamps();
            $table->unique(['deliverable_id', 'commit_hash']);
        });
    }

    public function down(): void { Schema::dropIfExists('github_commits'); }
};