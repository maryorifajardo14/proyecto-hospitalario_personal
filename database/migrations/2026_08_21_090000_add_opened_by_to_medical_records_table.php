<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ASII-10 - Maryori Fajardo (maryorifajardo14)
// Ajuste minimo y documentado sobre la tabla compartida `medical_records`
// (creada por el scaffold de ASII-11): agrega el autor de la apertura,
// exigido por la regla central de la actividad integradora ("cada entrada
// conserva autor y fecha"). No modifica ninguna columna existente ni el
// comportamiento de otros modulos que ya referencian medical_records.id.
// Ver docs/modulos/mod10/ADR-001-arquitectura.md, seccion 5.

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->foreignId('opened_by')
                ->nullable()
                ->after('opened_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropConstrainedForeignId('opened_by');
        });
    }
};
