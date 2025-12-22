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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporter_id');
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('category_id');
            $table->text('description')->nullable();
            $table->enum('urgency', ['Low', 'Medium', 'High'])->default('Low');
            $table->enum('status', ['Pending', 'Assigned', 'In_Progress', 'Completed'])->default('Pending');
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
            $table->datetime('due_at')->nullable();
            $table->datetime('completed_at')->nullable();

            $table->foreign('reporter_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('technician_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
