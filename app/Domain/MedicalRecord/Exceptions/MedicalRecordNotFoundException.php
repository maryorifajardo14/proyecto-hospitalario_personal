<?php

namespace App\Domain\MedicalRecord\Exceptions;

use RuntimeException;

/**
 * El paciente existe pero no tiene expediente abierto, o el expediente
 * solicitado no pertenece al tenant del usuario autenticado (RN-10-04).
 */
final class MedicalRecordNotFoundException extends RuntimeException
{
    public function __construct(string $message = 'Expediente no encontrado.')
    {
        parent::__construct($message);
    }
}
