<?php

namespace App\Domain\MedicalRecord;

/**
 * Puerto orientado al lenguaje del caso de uso (no es un CRUD generico).
 * Adaptadores: EloquentMedicalRecordRepository (PostgreSQL/Eloquent) e
 * InMemoryMedicalRecordRepository (doble de prueba), ambos en
 * app/Infrastructure/MedicalRecord.
 */
interface MedicalRecordRepository
{
    public function existsForPatient(string $tenantId, int $patientId): bool;

    public function findByPatientId(string $tenantId, int $patientId): ?MedicalRecordRecord;

    /**
     * Genera el siguiente numero correlativo (EXP-00001) para el tenant.
     */
    public function nextRecordNumber(string $tenantId): string;

    public function create(MedicalRecordRecord $record): MedicalRecordRecord;
}
