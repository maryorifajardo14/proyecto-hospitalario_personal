<?php

namespace Tests\Unit\Domain\MedicalRecord;

use App\Domain\MedicalRecord\AuthorizationContext;
use App\Domain\MedicalRecord\Exceptions\MedicalRecordAccessDeniedException;
use App\Domain\MedicalRecord\MedicalRecordAuthorizationChecker;
use PHPUnit\Framework\TestCase;

/**
 * Prueba de dominio/regla (RN-10-03): sin Eloquent, sin HTTP, sin Spatie.
 * Cubre "consulta autorizada" y "acceso denegado" de la evidencia mínima
 * pedida para el módulo ASII-10.
 */
class MedicalRecordAuthorizationCheckerTest extends TestCase
{
    private MedicalRecordAuthorizationChecker $checker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checker = new MedicalRecordAuthorizationChecker();
    }

    public function test_permite_abrir_expediente_con_rol_recepcionista(): void
    {
        $context = new AuthorizationContext(userId: 1, roles: ['Recepcionista']);

        $this->checker->authorizeOpen($context);

        $this->addToAssertionCount(1); // no lanza excepcion
    }

    public function test_deniega_abrir_expediente_sin_rol_autorizado(): void
    {
        $context = new AuthorizationContext(userId: 1, roles: ['TecnicoLab']);

        $this->expectException(MedicalRecordAccessDeniedException::class);

        $this->checker->authorizeOpen($context);
    }

    public function test_permite_consultar_expediente_con_rol_medico(): void
    {
        $context = new AuthorizationContext(userId: 2, roles: ['Médico']);

        $this->checker->authorizeView($context);

        $this->addToAssertionCount(1);
    }

    public function test_deniega_consultar_expediente_sin_rol_autorizado(): void
    {
        $context = new AuthorizationContext(userId: 2, roles: ['Recepcionista']);

        $this->expectException(MedicalRecordAccessDeniedException::class);

        $this->checker->authorizeView($context);
    }
}
