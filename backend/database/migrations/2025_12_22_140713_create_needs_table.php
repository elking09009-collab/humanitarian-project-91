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
    Schema::create('needs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('area_id')->constrained()->cascadeOnDelete();
        $table->enum('type', ['food', 'water', 'medicine', 'shelter', 'other']);
        $table->integer('quantity')->default(0);
        $table->enum('status', ['pending', 'delivered'])->default('pending');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('needs');
    }
};
