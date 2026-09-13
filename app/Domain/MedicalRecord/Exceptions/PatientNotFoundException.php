<?php

namespace App\Domain\MedicalRecord\Exceptions;

use RuntimeException;

/**
 * El paciente indicado no existe (o no pertenece al tenant del usuario).
 * ASII-10 no gestiona pacientes (ASII-03); solo puede reportar que no
 * encontro uno para abrir o consultar su expediente.
 */
final class PatientNotFoundException extends RuntimeException
{
    public function __construct(string $message = 'Paciente no encontrado.')
    {
        parent::__construct($message);
    }
}
