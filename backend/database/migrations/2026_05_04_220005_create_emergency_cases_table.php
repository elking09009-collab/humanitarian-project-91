<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('emergency_cases', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['surgery', 'fire', 'flood', 'accident', 'displacement', 'other'])->default('other');
            $table->decimal('needed_amount', 12, 2);
            $table->decimal('current_amount', 12, 2)->default(0);
            $table->string('contact_info')->nullable();
            $table->string('area')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->enum('status', ['urgent', 'funded', 'closed'])->default('urgent');
            $table->string('verified_by')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('emergency_cases'); }
};
