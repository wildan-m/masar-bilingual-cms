<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->nullable()->unique();
            $table->string('name', 120);
            $table->string('company', 160)->nullable();
            $table->string('email', 190);
            $table->string('phone', 40);
            $table->foreignId('sector_id')->nullable()->constrained()->nullOnDelete();
            $table->string('project_location', 160)->nullable();
            $table->text('message');
            $table->string('locale', 5);
            $table->string('attachment_disk', 40)->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_original_name')->nullable();
            $table->string('attachment_mime', 120)->nullable();
            $table->unsignedInteger('attachment_size')->nullable();
            $table->string('status', 20)->default('new')->index();
            $table->timestamp('status_changed_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_requests');
    }
};
