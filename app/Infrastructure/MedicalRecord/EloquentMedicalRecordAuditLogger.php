<?php

namespace App\Infrastructure\MedicalRecord;

use App\Domain\MedicalRecord\MedicalRecordAuditLogger;
use App\Models\MedicalRecordAccessLog;

/**
 * Adaptador PostgreSQL/Eloquent del puerto MedicalRecordAuditLogger.
 * Escribe en la tabla HOSPITAL local `medical_record_access_logs`
 * (RN-10-02: toda apertura o consulta se registra).
 */
final class EloquentMedicalRecordAuditLogger implements MedicalRecordAuditLogger
{
    public function logOpen(
        string $tenantId,
        int $patientId,
        int $userId,
        string $result,
        ?string $reason = null,
        ?int $medicalRecordId = null,
    ): void {
        $this->log('open', $tenantId, $patientId, $userId, $result, $reason, $medicalRecordId);
    }

    public function logView(
        string $tenantId,
        int $patientId,
        int $userId,
        string $result,
        ?string $reason = null,
        ?int $medicalRecordId = null,
    ): void {
        $this->log('view', $tenantId, $patientId, $userId, $result, $reason, $medicalRecordId);
    }

    private function log(
        string $action,
        string $tenantId,
        int $patientId,
        int $userId,
        string $result,
        ?string $reason,
        ?int $medicalRecordId,
    ): void {
        MedicalRecordAccessLog::query()->create([
            'tenant_id' => $tenantId,
            'medical_record_id' => $medicalRecordId,
            'patient_id' => $patientId,
            'user_id' => $userId,
            'action' => $action,
            'result' => $result,
            'reason' => $reason,
        ]);
    }
}
