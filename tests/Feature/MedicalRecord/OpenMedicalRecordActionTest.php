<?php

namespace Tests\Feature\MedicalRecord;

use App\Application\MedicalRecord\OpenMedicalRecordAction;
use App\Domain\MedicalRecord\AuthorizationContext;
use App\Domain\MedicalRecord\Exceptions\MedicalRecordAccessDeniedException;
use App\Domain\MedicalRecord\Exceptions\MedicalRecordAlreadyExistsException;
use App\Domain\MedicalRecord\Exceptions\PatientNotFoundException;
use App\Domain\MedicalRecord\MedicalRecordAuthorizationChecker;
use App\Infrastructure\MedicalRecord\InMemoryMedicalRecordRepository;
use Tests\Support\MedicalRecord\FakeMedicalRecordAuditLogger;
use Tests\Support\MedicalRecord\FakePatientExistenceChecker;
use Tests\TestCase;

/**
 * Pruebas de aplicación de UC-10-01 usando dobles de prueba
 * (InMemoryMedicalRecordRepository + fakes), sin tocar PostgreSQL.
 * Cubre "apertura duplicada" de la evidencia mínima del módulo.
 */
class OpenMedicalRecordActionTest extends TestCase
{
    private const TENANT = 'tenant-a';

    public function test_abre_expediente_exitosamente_con_rol_recepcionista(): void
    {
        $auditLogger = new FakeMedicalRecordAuditLogger();

        $action = new OpenMedicalRecordAction(
            repository: new InMemoryMedicalRecordRepository(),
            patients: new FakePatientExistenceChecker([self::TENANT => [10]]),
            authorization: new MedicalRecordAuthorizationChecker(),
            auditLogger: $auditLogger,
        );

        $record = $action->handle(
            self::TENANT,
            10,
            new AuthorizationContext(userId: 5, roles: ['Recepcionista'])
        );

        $this->assertSame('EXP-00001', $record->recordNumber);
        $this->assertSame(10, $record->patientId);
        $this->assertSame(5, $record->openedBy);
        $this->assertSame('success', $auditLogger->lastEntry()['result']);
    }

    public function test_rechaza_apertura_duplicada_para_el_mismo_paciente(): void
    {
        $repository = new InMemoryMedicalRecordRepository();
        $auditLogger = new FakeMedicalRecordAuditLogger();

        $action = new OpenMedicalRecordAction(
            repository: $repository,
            patients: new FakePatientExistenceChecker([self::TENANT => [10]]),
            authorization: new MedicalRecordAuthorizationChecker(),
            auditLogger: $auditLogger,
        );

        $context = new AuthorizationContext(userId: 5, roles: ['Recepcionista']);
        $action->handle(self::TENANT, 10, $context);

        $this->expectException(MedicalRecordAlreadyExistsException::class);

        try {
            $action->handle(self::TENANT, 10, $context);
        } finally {
            $this->assertSame('denied', $auditLogger->lastEntry()['result']);
            $this->assertSame('expediente_duplicado', $auditLogger->lastEntry()['reason']);
        }
    }

    public function test_deniega_apertura_sin_rol_autorizado(): void
    {
        $action = new OpenMedicalRecordAction(
            repository: new InMemoryMedicalRecordRepository(),
            patients: new FakePatientExistenceChecker([self::TENANT => [10]]),
            authorization: new MedicalRecordAuthorizationChecker(),
            auditLogger: new FakeMedicalRecordAuditLogger(),
        );

        $this->expectException(MedicalRecordAccessDeniedException::class);

        $action->handle(self::TENANT, 10, new AuthorizationContext(userId: 5, roles: ['TecnicoLab']));
    }

    public function test_lanza_paciente_no_encontrado_cuando_el_paciente_no_existe(): void
    {
        $action = new OpenMedicalRecordAction(
            repository: new InMemoryMedicalRecordRepository(),
            patients: new FakePatientExistenceChecker([self::TENANT => []]),
            authorization: new MedicalRecordAuthorizationChecker(),
            auditLogger: new FakeMedicalRecordAuditLogger(),
        );

        $this->expectException(PatientNotFoundException::class);

        $action->handle(self::TENANT, 999, new AuthorizationContext(userId: 5, roles: ['Recepcionista']));
    }
}
