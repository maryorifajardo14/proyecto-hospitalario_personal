<?php

namespace App\Domain\MedicalRecord;

use App\Domain\MedicalRecord\Exceptions\MedicalRecordAccessDeniedException;

/**
 * Materializa al actor "Sistema de Seguridad" (UC-10-03, Semana 1) como una
 * regla de dominio pura: decide permitido/denegado a partir de los roles ya
 * resueltos, sin tocar Spatie, Eloquent ni HTTP (RN-10-03).
 */
final class MedicalRecordAuthorizationChecker
{
    /**
     * @var string[]
     */
    private const ROLES_OPEN = ['Recepcionista', 'Admin'];

    /**
     * @var string[]
     */
    private const ROLES_VIEW = ['Médico', 'Enfermera', 'Admin'];

    /**
     * @throws MedicalRecordAccessDeniedException
     */
    public function authorizeOpen(AuthorizationContext $context): void
    {
        if (! $context->hasAnyRole(self::ROLES_OPEN)) {
            throw new MedicalRecordAccessDeniedException(
                'No tiene autorización para abrir expedientes.'
            );
        }
    }

    /**
     * @throws MedicalRecordAccessDeniedException
     */
    public function authorizeView(AuthorizationContext $context): void
    {
        if (! $context->hasAnyRole(self::ROLES_VIEW)) {
            throw new MedicalRecordAccessDeniedException(
                'No tiene autorización para consultar el expediente.'
            );
        }
    }
}
