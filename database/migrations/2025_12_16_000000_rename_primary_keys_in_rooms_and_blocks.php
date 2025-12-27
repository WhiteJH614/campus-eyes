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
        // 1. Drop foreign keys that reference the columns we are about to rename
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['block_id']);
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
        });

        // 2. Rename the columns
        Schema::table('blocks', function (Blueprint $table) {
            $table->renameColumn('block_id', 'id');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->renameColumn('room_id', 'id');
        });

        // 3. Re-add the foreign keys pointing to the new column names
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreign('block_id')
                ->references('id')
                ->on('blocks')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop the new foreign keys
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['block_id']);
        });

        // 2. Rename columns back
        Schema::table('rooms', function (Blueprint $table) {
            $table->renameColumn('id', 'room_id');
        });

        Schema::table('blocks', function (Blueprint $table) {
            $table->renameColumn('id', 'block_id');
        });

        // 3. Restore original foreign keys
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreign('block_id')
                ->references('block_id')
                ->on('blocks')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('room_id')
                ->references('room_id')
                ->on('rooms')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }
};
