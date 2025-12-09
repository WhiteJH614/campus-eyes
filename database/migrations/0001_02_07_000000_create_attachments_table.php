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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();

            // FK to reports with cascade delete
            $table->foreignId('report_id')
                ->constrained('reports')
                ->cascadeOnDelete();

            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->string('file_type', 50)->nullable();

            // Match ENUM from your SQL
            $table->enum('attachment_type', ['REPORTER_PROOF', 'TECHNICIAN_PROOF']);

            // Default CURRENT_TIMESTAMP
            $table->dateTime('uploaded_at')->useCurrent();

            // No created_at / updated_at in your SQL, so skip $table->timestamps()
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
