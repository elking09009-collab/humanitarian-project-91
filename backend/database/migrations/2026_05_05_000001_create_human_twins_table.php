<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Needy families (no FK on creation)
        Schema::create('twin_families', function (Blueprint $table) {
            $table->id();
            $table->string('family_head_name');
            $table->string('phone')->unique();
            $table->string('city');
            $table->string('area')->nullable();
            $table->unsignedInteger('members_count')->default(1);
            $table->text('needs');                  // JSON: ['food','education','medical','psychological']
            $table->text('story')->nullable();
            $table->enum('status', ['pending', 'active', 'matched', 'completed'])->default('pending');
            $table->unsignedBigInteger('matched_supporter_id')->nullable();
            $table->timestamps();
        });

        // Supporting families
        Schema::create('twin_supporters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('city');
            $table->text('support_types');          // JSON: ['financial','consulting','emotional','educational']
            $table->unsignedInteger('monthly_budget')->default(0);
            $table->text('bio')->nullable();
            $table->enum('status', ['pending', 'active', 'matched', 'paused'])->default('pending');
            $table->unsignedBigInteger('matched_family_id')->nullable();
            $table->timestamps();
        });

        // Add FK after both tables exist
        Schema::table('twin_families', function (Blueprint $table) {
            $table->foreign('matched_supporter_id')->references('id')->on('twin_supporters')->nullOnDelete();
        });
        Schema::table('twin_supporters', function (Blueprint $table) {
            $table->foreign('matched_family_id')->references('id')->on('twin_families')->nullOnDelete();
        });

        // Messages between matched pairs
        Schema::create('twin_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supporter_id');
            $table->unsignedBigInteger('family_id');
            $table->enum('sender_type', ['supporter', 'family', 'platform']);
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->foreign('supporter_id')->references('id')->on('twin_supporters')->cascadeOnDelete();
            $table->foreign('family_id')->references('id')->on('twin_families')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twin_messages');
        Schema::table('twin_supporters', function (Blueprint $table) {
            $table->dropForeign(['matched_family_id']);
        });
        Schema::table('twin_families', function (Blueprint $table) {
            $table->dropForeign(['matched_supporter_id']);
        });
        Schema::dropIfExists('twin_supporters');
        Schema::dropIfExists('twin_families');
    }
};
