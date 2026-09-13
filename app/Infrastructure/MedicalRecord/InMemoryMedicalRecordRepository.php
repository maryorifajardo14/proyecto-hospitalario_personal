<?php

namespace App\Infrastructure\MedicalRecord;

use App\Domain\MedicalRecord\MedicalRecordRecord;
use App\Domain\MedicalRecord\MedicalRecordRepository;

/**
 * Doble de prueba (fake) del puerto MedicalRecordRepository. Usado por las
 * pruebas de aplicacion (OpenMedicalRecordActionTest, ViewMedicalRecordActionTest)
 * para verificar la orquestacion sin tocar la base de datos.
 */
final class InMemoryMedicalRecordRepository implements MedicalRecordRepository
{
    /**
     * @var MedicalRecordRecord[]
     */
    private array $records = [];

    private int $nextId = 1;

    public function existsForPatient(string $tenantId, int $patientId): bool
    {
        return $this->findByPatientId($tenantId, $patientId) !== null;
    }

    public function findByPatientId(string $tenantId, int $patientId): ?MedicalRecordRecord
    {
        foreach ($this->records as $record) {
            if ($record->tenantId === $tenantId && $record->patientId === $patientId) {
                return $record;
            }
        }

        return null;
    }

    public function nextRecordNumber(string $tenantId): string
    {
        $count = 0;

        foreach ($this->records as $record) {
            if ($record->tenantId === $tenantId) {
                $count++;
            }
        }

        return sprintf('EXP-%05d', $count + 1);
    }

    public function create(MedicalRecordRecord $record): MedicalRecordRecord
    {
        $stored = new MedicalRecordRecord(
            id: $this->nextId++,
            tenantId: $record->tenantId,
            patientId: $record->patientId,
            recordNumber: $record->recordNumber,
            openedAt: $record->openedAt,
            openedBy: $record->openedBy,
            background: $record->background,
            familyBackground: $record->familyBackground,
            surgicalHistory: $record->surgicalHistory,
            obstetricHistory: $record->obstetricHistory,
        );

        $this->records[] = $stored;

        return $stored;
    }
}
