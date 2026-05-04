<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('legacy_givings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('full_name');
            $table->string('national_id')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable(); // % of savings
            $table->enum('trigger_event', ['death', 'incapacity', 'scheduled'])->default('death');
            $table->string('beneficiary_category')->nullable(); // orphans/elderly/education
            $table->text('notes')->nullable();
            $table->string('legal_document_url')->nullable();
            $table->enum('status', ['pending', 'active', 'executed'])->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('legacy_givings'); }
};
