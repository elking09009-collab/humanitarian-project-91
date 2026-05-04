<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crisis_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('area_id')->nullable();
            $table->string('area_name')->nullable();
            $table->enum('severity', ['critical', 'high', 'medium', 'low'])->default('high');
            $table->enum('type', ['weather', 'epidemic', 'displacement', 'food', 'economic', 'other'])->default('other');
            $table->text('needed_items')->nullable();
            $table->decimal('needed_amount', 12, 2)->default(0);
            $table->decimal('current_amount', 12, 2)->default(0);
            $table->enum('status', ['active', 'resolved', 'expired'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('crisis_alerts'); }
};
