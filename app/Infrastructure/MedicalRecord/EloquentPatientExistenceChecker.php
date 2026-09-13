<?php

namespace App\Infrastructure\MedicalRecord;

use App\Domain\MedicalRecord\PatientExistenceChecker;
use App\Models\Patient;

/**
 * Adaptador de solo lectura sobre el modelo Eloquent Patient (propiedad de
 * ASII-03). ASII-10 nunca escribe en `patients`, solo verifica existencia
 * dentro del tenant antes de abrir un expediente.
 */
final class EloquentPatientExistenceChecker implements PatientExistenceChecker
{
    public function exists(string $tenantId, int $patientId): bool
    {
        return Patient::query()
            ->where('tenant_id', $tenantId)
            ->where('id', $patientId)
            ->exists();
    }
}
