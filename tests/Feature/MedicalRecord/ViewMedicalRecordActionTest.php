<?php

namespace Tests\Feature\MedicalRecord;

use App\Application\MedicalRecord\ViewMedicalRecordAction;
use App\Domain\MedicalRecord\AuthorizationContext;
use App\Domain\MedicalRecord\Exceptions\MedicalRecordAccessDeniedException;
use App\Domain\MedicalRecord\Exceptions\MedicalRecordNotFoundException;
use App\Domain\MedicalRecord\MedicalRecordAuthorizationChecker;
use App\Domain\MedicalRecord\MedicalRecordRecord;
use App\Infrastructure\MedicalRecord\InMemoryMedicalRecordRepository;
use DateTimeImmutable;
use Tests\Support\MedicalRecord\FakeMedicalRecordAuditLogger;
use Tests\TestCase;

/**
 * Pruebas de aplicación de UC-10-02. Cubre "consulta autorizada" y "acceso
 * denegado" de la evidencia mínima del módulo ASII-10.
 */
class ViewMedicalRecordActionTest extends TestCase
{
    private const TENANT = 'tenant-a';

    private function repositoryWithOneRecord(): InMemoryMedicalRecordRepository
    {
        $repository = new InMemoryMedicalRecordRepository();

        $repository->create(new MedicalRecordRecord(
            id: null,
            tenantId: self::TENANT,
            patientId: 10,
            recordNumber: 'EXP-00001',
            openedAt: new DateTimeImmutable('2026-08-01'),
            openedBy: 5,
        ));

        return $repository;
    }

    public function test_consulta_autorizada_retorna_el_expediente(): void
    {
        $auditLogger = new FakeMedicalRecordAuditLogger();

        $action = new ViewMedicalRecordAction(
            repository: $this->repositoryWithOneRecord(),
            authorization: new MedicalRecordAuthorizationChecker(),
            auditLogger: $auditLogger,
        );

        $record = $action->handle(
            self::TENANT,
            10,
            new AuthorizationContext(userId: 7, roles: ['Médico'])
        );

        $this->assertSame('EXP-00001', $record->recordNumber);
        $this->assertSame('success', $auditLogger->lastEntry()['result']);
    }

    public function test_deniega_consulta_sin_rol_autorizado(): void
    {
        $auditLogger = new FakeMedicalRecordAuditLogger();

        $action = new ViewMedicalRecordAction(
            repository: $this->repositoryWithOneRecord(),
            authorization: new MedicalRecordAuthorizationChecker(),
            auditLogger: $auditLogger,
        );

        try {
            $action->handle(self::TENANT, 10, new AuthorizationContext(userId: 7, roles: ['Recepcionista']));
            $this->fail('Se esperaba MedicalRecordAccessDeniedException.');
        } catch (MedicalRecordAccessDeniedException $exception) {
            $this->assertSame('denied', $auditLogger->lastEntry()['result']);
        }
    }

    public function test_lanza_no_encontrado_cuando_el_paciente_no_tiene_expediente(): void
    {
        $action = new ViewMedicalRecordAction(
            repository: new InMemoryMedicalRecordRepository(),
            authorization: new MedicalRecordAuthorizationChecker(),
            auditLogger: new FakeMedicalRecordAuditLogger(),
        );

        $this->expectException(MedicalRecordNotFoundException::class);

        $action->handle(self::TENANT, 999, new AuthorizationContext(userId: 7, roles: ['Médico']));
    }
}
