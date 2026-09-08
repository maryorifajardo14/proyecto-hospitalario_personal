<?php

namespace App\Domain\MedicalRecord\Exceptions;

use RuntimeException;

/**
 * RN-10-03: ningun usuario sin rol autorizado puede abrir ni consultar el
 * expediente. Se traduce a 403 en MedicalRecordController.
 */
final class MedicalRecordAccessDeniedException extends RuntimeException
{
    public function __construct(string $message = 'Acceso denegado.')
    {
        parent::__construct($message);
    }
}
