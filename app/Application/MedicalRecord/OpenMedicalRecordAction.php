<?php

namespace App\Application\MedicalRecord;

use App\Domain\MedicalRecord\AuthorizationContext;
use App\Domain\MedicalRecord\Exceptions\MedicalRecordAccessDeniedException;
use App\Domain\MedicalRecord\Exceptions\MedicalRecordAlreadyExistsException;
use App\Domain\MedicalRecord\Exceptions\PatientNotFoundException;
use App\Domain\MedicalRecord\MedicalRecordAuditLogger;
use App\Domain\MedicalRecord\MedicalRecordAuthorizationChecker;
use App\Domain\MedicalRecord\MedicalRecordRecord;
use App\Domain\MedicalRecord\MedicalRecordRepository;
use App\Domain\MedicalRecord\PatientExistenceChecker;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Orquesta UC-10-01 (apertura de expediente). Depende exclusivamente de
 * abstracciones (puertos del dominio); no conoce Eloquent ni PostgreSQL.
 */
final class OpenMedicalRecordAction
{
    public function __construct(
        private readonly MedicalRecordRepository $repository,
        private readonly PatientExistenceChecker $patients,
        private readonly MedicalRecordAuthorizationChecker $authorization,
        private readonly MedicalRecordAuditLogger $auditLogger,
    ) {
    }

    /**
     * @throws MedicalRecordAccessDeniedException
     * @throws PatientNotFoundException
     * @throws MedicalRecordAlreadyExistsException
     */
    public function handle(string $tenantId, int $patientId, AuthorizationContext $context): MedicalRecordRecord
    {
        try {
            $this->authorization->authorizeOpen($context);
        } catch (MedicalRecordAccessDeniedException $exception) {
            $this->auditLogger->logOpen($tenantId, $patientId, $context->userId, 'denied', $exception->getMessage());

            throw $exception;
        }

        if (! $this->patients->exists($tenantId, $patientId)) {
            $this->auditLogger->logOpen($tenantId, $patientId, $context->userId, 'denied', 'paciente_no_encontrado');

            throw new PatientNotFoundException();
        }

        return DB::transaction(function () use ($tenantId, $patientId, $context) {
            $existing = $this->repository->findByPatientId($tenantId, $patientId);

            if ($existing !== null) {
                $this->auditLogger->logOpen(
                    $tenantId,
                    $patientId,
                    $context->userId,
                    'denied',
                    'expediente_duplicado',
                    $existing->id
                );

                throw new MedicalRecordAlreadyExistsException($existing);
            }

            $record = new MedicalRecordRecord(
                id: null,
                tenantId: $tenantId,
                patientId: $patientId,
                recordNumber: $this->repository->nextRecordNumber($tenantId),
                openedAt: new DateTimeImmutable(),
                openedBy: $context->userId,
            );

            $created = $this->repository->create($record);

            $this->auditLogger->logOpen($tenantId, $patientId, $context->userId, 'success', null, $created->id);

            return $created;
        });
    }
}
