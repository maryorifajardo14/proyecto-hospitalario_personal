<?php

namespace App\Domain\MedicalRecord;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Entidad de dominio del expediente medico electronico base (ASII-10).
 *
 * Deliberadamente no extiende Eloquent ni conoce PostgreSQL: representa la
 * regla central de la actividad integradora ("existe un expediente
 * longitudinal por paciente; cada entrada conserva autor y fecha") sin
 * depender de infraestructura.
 */
final class MedicalRecordRecord
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $tenantId,
        public readonly int $patientId,
        public readonly string $recordNumber,
        public readonly DateTimeImmutable $openedAt,
        public readonly ?int $openedBy,
        public readonly ?string $background = null,
        public readonly ?string $familyBackground = null,
        public readonly ?string $surgicalHistory = null,
        public readonly ?string $obstetricHistory = null,
    ) {
        if (trim($tenantId) === '') {
            throw new InvalidArgumentException('El expediente debe pertenecer a un tenant.');
        }

        if ($patientId <= 0) {
            throw new InvalidArgumentException('El expediente debe estar asociado a un paciente valido.');
        }

        if (! preg_match('/^EXP-\d{5,}$/', $recordNumber)) {
            throw new InvalidArgumentException(
                "El numero de expediente '{$recordNumber}' no cumple el formato EXP-00001."
            );
        }
    }

    /**
     * RN-10-02: toda apertura debe conservar quien la realizo.
     */
    public function hasAuthor(): bool
    {
        return $this->openedBy !== null;
    }
}
