<?php

namespace App\Domain\MedicalRecord\Exceptions;

use App\Domain\MedicalRecord\MedicalRecordRecord;
use RuntimeException;

/**
 * RN-10-01: un paciente solo puede tener un expediente longitudinal.
 */
final class MedicalRecordAlreadyExistsException extends RuntimeException
{
    public function __construct(
        public readonly MedicalRecordRecord $existing,
    ) {
        parent::__construct('El paciente ya tiene un expediente médico.');
    }
}
