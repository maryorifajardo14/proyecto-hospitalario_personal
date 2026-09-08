<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ASII-10 - Maryori Fajardo (maryorifajardo14)
// Bitacora local HOSPITAL de aperturas y consultas del expediente (RN-10-02).
// Propiedad exclusiva de ASII-10; no reemplaza la auditoria transversal de
// ASII-22 (cross_access_audit / local_access_audit), que podra alimentarse
// de esta tabla en una fase posterior via outbox/event_id (fuera del MVP).
// Ver docs/modulos/mod10/ADR-001-arquitectura.md, seccion 2 y 5.

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_record_access_logs', function (Blueprint $table) {
            $table->id();
            $table->char('tenant_id', 36)->index();
            $table->foreignId('medical_record_id')
                ->nullable()
                ->constrained('medical_records')
                ->nullOnDelete();
            // Sin FK a `patients`: la bitacora tambien debe poder registrar
            // intentos contra un patient_id que no existe (denegado antes de
            // resolver el paciente); ver docs/modulos/mod10/ADR-001-arquitectura.md.
            $table->unsignedBigInteger('patient_id');
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->enum('action', ['open', 'view']);
            $table->enum('result', ['success', 'denied']);
            $table->string('reason', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'patient_id', 'created_at'], 'idx_mr_access_tenant_patient_date');
            $table->index(['tenant_id', 'action', 'result'], 'idx_mr_access_tenant_action_result');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_record_access_logs');
    }
};
