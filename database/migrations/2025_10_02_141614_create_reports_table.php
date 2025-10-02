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
            $table->foreignId('analysis_id')->unique()->constrained()->onDelete('cascade');
            $table->decimal('total_similarity_percentage', 5, 2);
            $table->text('executive_summary')->nullable();
            $table->timestamps(); // Corresponde a fecha_generacion (created_at)
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
