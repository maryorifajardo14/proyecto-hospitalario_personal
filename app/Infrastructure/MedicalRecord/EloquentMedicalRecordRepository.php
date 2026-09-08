<?php

namespace App\Infrastructure\MedicalRecord;

use App\Domain\MedicalRecord\MedicalRecordRecord;
use App\Domain\MedicalRecord\MedicalRecordRepository;
use App\Models\MedicalRecord;

/**
 * Adaptador PostgreSQL/Eloquent del puerto MedicalRecordRepository. Todas
 * las consultas filtran por tenant_id (RN-10-04): ningun expediente de otro
 * hospital puede leerse ni contarse desde aqui.
 */
final class EloquentMedicalRecordRepository implements MedicalRecordRepository
{
    public function existsForPatient(string $tenantId, int $patientId): bool
    {
        return MedicalRecord::query()
            ->where('tenant_id', $tenantId)
            ->where('patient_id', $patientId)
            ->exists();
    }

    public function findByPatientId(string $tenantId, int $patientId): ?MedicalRecordRecord
    {
        $model = MedicalRecord::query()
            ->where('tenant_id', $tenantId)
            ->where('patient_id', $patientId)
            ->first();

        return $model === null ? null : $this->toDomain($model);
    }

    public function nextRecordNumber(string $tenantId): string
    {
        $count = MedicalRecord::query()
            ->where('tenant_id', $tenantId)
            ->lockForUpdate()
            ->count();

        return sprintf('EXP-%05d', $count + 1);
    }

    public function create(MedicalRecordRecord $record): MedicalRecordRecord
    {
        $model = MedicalRecord::query()->create([
            'tenant_id' => $record->tenantId,
            'patient_id' => $record->patientId,
            'record_number' => $record->recordNumber,
            'opened_at' => $record->openedAt,
            'opened_by' => $record->openedBy,
            'background' => $record->background,
            'family_background' => $record->familyBackground,
            'surgical_history' => $record->surgicalHistory,
            'obstetric_history' => $record->obstetricHistory,
        ]);

        return $this->toDomain($model);
    }

    private function toDomain(MedicalRecord $model): MedicalRecordRecord
    {
        return new MedicalRecordRecord(
            id: $model->id,
            tenantId: (string) $model->tenant_id,
            patientId: (int) $model->patient_id,
            recordNumber: $model->record_number,
            openedAt: $model->opened_at->toDateTimeImmutable(),
            openedBy: $model->opened_by !== null ? (int) $model->opened_by : null,
            background: $model->background,
            familyBackground: $model->family_background,
            surgicalHistory: $model->surgical_history,
            obstetricHistory: $model->obstetric_history,
        );
    }
}
