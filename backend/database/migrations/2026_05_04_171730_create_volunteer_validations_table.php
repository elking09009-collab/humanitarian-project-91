<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('volunteer_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('need_id')->constrained('needs')->cascadeOnDelete();
            $table->foreignId('volunteer_id')->constrained('users')->cascadeOnDelete();
            $table->text('field_notes')->nullable();
            $table->json('document_urls')->nullable(); // array of uploaded doc URLs
            $table->string('status')->default('submitted'); // submitted, approved, rejected
            $table->string('admin_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_validations');
    }
};
