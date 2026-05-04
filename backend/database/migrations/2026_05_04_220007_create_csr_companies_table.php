<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('csr_companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('logo_url')->nullable();
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->string('sector')->nullable();
            $table->decimal('total_donated', 12, 2)->default(0);
            $table->decimal('matching_ratio', 5, 2)->default(1.0); // 1x = match 1:1
            $table->enum('badge_level', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->enum('status', ['active', 'pending', 'suspended'])->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('csr_companies'); }
};
