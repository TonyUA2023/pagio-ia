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
        Schema::table('documents', function (Blueprint $table) {
            // Columna para el conteo de palabras extraídas del PDF.
            $table->unsignedInteger('word_count')->nullable()->after('status');
            // Columna para el tamaño del archivo en kilobytes (KB).
            $table->unsignedInteger('file_size')->nullable()->after('word_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['word_count', 'file_size']);
        });
    }
};