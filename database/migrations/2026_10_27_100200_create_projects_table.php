<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('slug');
            $table->json('location')->nullable();
            $table->json('summary')->nullable();
            $table->json('body')->nullable();
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->foreignId('sector_id')->nullable()->constrained()->nullOnDelete();
            $table->string('client_type', 30)->nullable();
            $table->unsignedSmallInteger('completion_year')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['is_published', 'completion_year']);
        });

        Schema::create('discipline_project', function (Blueprint $table) {
            $table->foreignId('discipline_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['discipline_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discipline_project');
        Schema::dropIfExists('projects');
    }
};
