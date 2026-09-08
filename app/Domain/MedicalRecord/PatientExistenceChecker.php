<?php

namespace App\Domain\MedicalRecord;

/**
 * Puerto de solo lectura hacia el paciente (propiedad de ASII-03). ASII-10
 * nunca escribe ni gestiona pacientes; solo necesita saber si existe dentro
 * del tenant antes de abrir o consultar un expediente.
 */
interface PatientExistenceChecker
{
    public function exists(string $tenantId, int $patientId): bool;
}
