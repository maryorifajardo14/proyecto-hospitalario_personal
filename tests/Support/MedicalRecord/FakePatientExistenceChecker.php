<?php

namespace Tests\Support\MedicalRecord;

use App\Domain\MedicalRecord\PatientExistenceChecker;

/**
 * Doble de prueba del puerto PatientExistenceChecker: simula qué pacientes
 * "existen" en un tenant sin tocar la tabla `patients` (propiedad de ASII-03).
 */
final class FakePatientExistenceChecker implements PatientExistenceChecker
{
    /**
     * @param  array<string, int[]>  $existingByTenant
     */
    public function __construct(
        private array $existingByTenant = [],
    ) {
    }

    public function exists(string $tenantId, int $patientId): bool
    {
        return in_array($patientId, $this->existingByTenant[$tenantId] ?? [], true);
    }
}
