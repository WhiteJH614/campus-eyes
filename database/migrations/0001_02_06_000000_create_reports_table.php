<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('reporter_id')
                ->constrained('users'); // reporter (must exist)

            $table->foreignId('technician_id')
                ->nullable()
                ->constrained('users'); // assigned technician (optional)

            $table->foreignId('room_id')
                ->constrained('rooms'); // location

            $table->foreignId('category_id')
                ->constrained('categories'); // category of issue

            // Core data
            $table->text('description')->nullable();

            // Match your SQL ENUM definitions (case-sensitive)
            $table->enum('urgency', ['Low', 'Medium', 'High'])
                ->default('Low');

            $table->enum('status', ['Pending', 'Assigned', 'In_Progress', 'Completed'])
                ->default('Pending');

            $table->text('resolution_notes')->nullable();

            $table->dateTime('due_at')->nullable();
            $table->dateTime('completed_at')->nullable();

            // created_at / updated_at
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
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
