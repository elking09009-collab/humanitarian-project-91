<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['medicine', 'equipment', 'food'])->default('medicine');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('condition')->nullable(); // good/used/new
            $table->unsignedBigInteger('donor_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->enum('status', ['available', 'reserved', 'delivered'])->default('available');
            $table->date('expiry_date')->nullable();
            $table->string('contact_info')->nullable();
            $table->text('pharmacist_notes')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('inventory_items'); }
};
