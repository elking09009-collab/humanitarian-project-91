<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('impact_stories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('audio_url')->nullable();
            $table->string('author_name');
            $table->string('city')->nullable();
            $table->enum('category', ['health', 'education', 'food', 'housing', 'livelihood', 'other'])->default('other');
            $table->integer('likes_count')->default(0);
            $table->string('cover_image')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('author_name')->nullable();
            $table->string('title');
            $table->text('content');
            $table->string('city')->nullable();
            $table->enum('category', ['logistics', 'medical', 'legal', 'experience', 'general'])->default('general');
            $table->integer('replies_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('impact_stories');
    }
};
