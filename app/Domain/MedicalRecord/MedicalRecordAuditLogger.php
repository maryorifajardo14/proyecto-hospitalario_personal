<?php

namespace App\Domain\MedicalRecord;

/**
 * Puerto de bitacora (RN-10-02: toda apertura o consulta se registra).
 * Adaptador real: EloquentMedicalRecordAuditLogger, que escribe en la tabla
 * HOSPITAL local `medical_record_access_logs`.
 */
interface MedicalRecordAuditLogger
{
    public function logOpen(
        string $tenantId,
        int $patientId,
        int $userId,
        string $result,
        ?string $reason = null,
        ?int $medicalRecordId = null,
    ): void;

    public function logView(
        string $tenantId,
        int $patientId,
        int $userId,
        string $result,
        ?string $reason = null,
        ?int $medicalRecordId = null,
    ): void;
}
