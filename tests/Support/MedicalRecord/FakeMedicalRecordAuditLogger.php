<?php

namespace Tests\Support\MedicalRecord;

use App\Domain\MedicalRecord\MedicalRecordAuditLogger;

/**
 * Doble de prueba del puerto MedicalRecordAuditLogger para las pruebas de
 * aplicación (OpenMedicalRecordActionTest, ViewMedicalRecordActionTest):
 * guarda cada llamada en memoria en vez de escribir en base de datos.
 */
final class FakeMedicalRecordAuditLogger implements MedicalRecordAuditLogger
{
    /**
     * @var array<int, array{action: string, tenantId: string, patientId: int, userId: int, result: string, reason: ?string, medicalRecordId: ?int}>
     */
    public array $entries = [];

    public function logOpen(
        string $tenantId,
        int $patientId,
        int $userId,
        string $result,
        ?string $reason = null,
        ?int $medicalRecordId = null,
    ): void {
        $this->entries[] = compact('tenantId', 'patientId', 'userId', 'result', 'reason', 'medicalRecordId') + ['action' => 'open'];
    }

    public function logView(
        string $tenantId,
        int $patientId,
        int $userId,
        string $result,
        ?string $reason = null,
        ?int $medicalRecordId = null,
    ): void {
        $this->entries[] = compact('tenantId', 'patientId', 'userId', 'result', 'reason', 'medicalRecordId') + ['action' => 'view'];
    }

    public function lastEntry(): ?array
    {
        return $this->entries === [] ? null : end($this->entries);
    }
}
