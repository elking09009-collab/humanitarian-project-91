<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('micro_endowments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['waqf', 'loan'])->default('waqf');
            $table->decimal('goal_amount', 12, 2)->default(0);
            $table->decimal('current_amount', 12, 2)->default(0);
            $table->decimal('return_rate', 5, 2)->default(0); // % annual return for waqf
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->string('beneficiary_category')->nullable();
            $table->enum('status', ['active', 'completed', 'paused'])->default('active');
            $table->timestamps();
        });

        Schema::create('good_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('endowment_id')->nullable();
            $table->unsignedBigInteger('borrower_id')->nullable();
            $table->string('borrower_name');
            $table->decimal('amount', 12, 2);
            $table->text('purpose');
            $table->enum('status', ['active', 'repaid', 'forwarded'])->default('active');
            $table->timestamp('repaid_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('good_loans');
        Schema::dropIfExists('micro_endowments');
    }
};
