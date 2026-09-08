<?php

namespace Tests\Unit\Domain\MedicalRecord;

use App\Domain\MedicalRecord\MedicalRecordRecord;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Prueba de dominio/regla: la entidad MedicalRecordRecord no permite
 * construirse en un estado inconsistente con la regla central de la
 * actividad integradora ("cada entrada conserva autor y fecha").
 */
class MedicalRecordRecordTest extends TestCase
{
    public function test_construye_un_expediente_valido_con_autor(): void
    {
        $record = new MedicalRecordRecord(
            id: 1,
            tenantId: 'tenant-uuid',
            patientId: 10,
            recordNumber: 'EXP-00001',
            openedAt: new DateTimeImmutable('2026-08-21'),
            openedBy: 99,
        );

        $this->assertTrue($record->hasAuthor());
        $this->assertSame('EXP-00001', $record->recordNumber);
    }

    public function test_rechaza_numero_de_expediente_con_formato_invalido(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new MedicalRecordRecord(
            id: null,
            tenantId: 'tenant-uuid',
            patientId: 10,
            recordNumber: 'EXPEDIENTE-1',
            openedAt: new DateTimeImmutable(),
            openedBy: 1,
        );
    }

    public function test_rechaza_expediente_sin_paciente_valido(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new MedicalRecordRecord(
            id: null,
            tenantId: 'tenant-uuid',
            patientId: 0,
            recordNumber: 'EXP-00001',
            openedAt: new DateTimeImmutable(),
            openedBy: 1,
        );
    }

    public function test_reporta_que_no_tiene_autor_cuando_opened_by_es_nulo(): void
    {
        $record = new MedicalRecordRecord(
            id: null,
            tenantId: 'tenant-uuid',
            patientId: 10,
            recordNumber: 'EXP-00002',
            openedAt: new DateTimeImmutable(),
            openedBy: null,
        );

        $this->assertFalse($record->hasAuthor());
    }
}
