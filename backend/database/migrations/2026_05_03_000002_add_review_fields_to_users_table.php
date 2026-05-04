<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
            $table->boolean('can_review_accounts')->default(false)->after('rejection_reason');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('can_review_accounts');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'can_review_accounts', 'reviewed_by', 'reviewed_at']);
        });
    }
};
