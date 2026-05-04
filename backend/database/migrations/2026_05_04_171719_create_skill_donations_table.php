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
        Schema::create('skill_donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('need_id')->nullable()->constrained('needs')->nullOnDelete();
            $table->string('skill_type'); // medical, teaching, tech, legal, other
            $table->string('skill_title');
            $table->text('description');
            $table->unsignedTinyInteger('hours_offered')->default(1);
            $table->string('contact_info')->nullable();
            $table->string('status')->default('available'); // available, matched, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skill_donations');
    }
};
