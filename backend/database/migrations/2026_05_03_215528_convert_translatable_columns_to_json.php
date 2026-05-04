<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * SQLite doesn't support ALTER COLUMN, so we recreate the tables.
     * Safe to run only when no data exists (new install).
     */
    public function up(): void
    {
        // Convert areas.name and areas.description to TEXT (JSON-compatible)
        // SQLite ignores column type constraints; we just change the ORM declaration.
        // For a fresh install with no data we can use raw ALTER via recreate.
        // Since SQLite stores everything as TEXT anyway, we record the intent
        // by dropping & adding with json type using the SQLite table-rebuild approach.

        DB::statement('PRAGMA foreign_keys = OFF;');

        // --- areas: rename, recreate, copy, drop ---
        DB::statement('ALTER TABLE areas RENAME TO areas_old;');
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->json('name');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('priority_level')->default('low');
            $table->string('status')->default('active');
            $table->json('description')->nullable();
            $table->timestamps();
        });
        DB::statement('INSERT INTO areas (id, organization_id, name, latitude, longitude, priority_level, status, description, created_at, updated_at)
            SELECT id, organization_id,
                   json_object("ar", name),
                   latitude, longitude, COALESCE(priority_level,"low"), COALESCE(status,"active"),
                   CASE WHEN description IS NOT NULL THEN json_object("ar", description) ELSE NULL END,
                   created_at, updated_at FROM areas_old;');
        DB::statement('DROP TABLE areas_old;');

        // --- needs: rename, recreate, copy, drop ---
        DB::statement('ALTER TABLE needs RENAME TO needs_old;');
        Schema::create('needs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->unsignedInteger('quantity')->default(0);
            $table->string('status')->default('pending');
            $table->json('notes')->nullable();
            $table->timestamps();
        });
        DB::statement('INSERT INTO needs (id, organization_id, area_id, type, quantity, status, notes, created_at, updated_at)
            SELECT id, organization_id, area_id,
                   type,
                   quantity, COALESCE(status,"pending"),
                   CASE WHEN notes IS NOT NULL THEN json_object("ar", notes) ELSE NULL END,
                   created_at, updated_at FROM needs_old;');
        DB::statement('DROP TABLE needs_old;');

        DB::statement('PRAGMA foreign_keys = ON;');
    }

    public function down(): void
    {
        // Irreversible for simplicity (no data loss risk for fresh installs)
    }
};
