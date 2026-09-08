<?php

namespace App\Application\MedicalRecord;

use App\Domain\MedicalRecord\AuthorizationContext;
use App\Domain\MedicalRecord\Exceptions\MedicalRecordAccessDeniedException;
use App\Domain\MedicalRecord\Exceptions\MedicalRecordNotFoundException;
use App\Domain\MedicalRecord\MedicalRecordAuditLogger;
use App\Domain\MedicalRecord\MedicalRecordAuthorizationChecker;
use App\Domain\MedicalRecord\MedicalRecordRecord;
use App\Domain\MedicalRecord\MedicalRecordRepository;

/**
 * Orquesta UC-10-02 (consulta longitudinal autorizada). Igual que
 * OpenMedicalRecordAction, solo depende de puertos del dominio.
 */
final class ViewMedicalRecordAction
{
    public function __construct(
        private readonly MedicalRecordRepository $repository,
        private readonly MedicalRecordAuthorizationChecker $authorization,
        private readonly MedicalRecordAuditLogger $auditLogger,
    ) {
    }

    /**
     * @throws MedicalRecordAccessDeniedException
     * @throws MedicalRecordNotFoundException
     */
    public function handle(string $tenantId, int $patientId, AuthorizationContext $context): MedicalRecordRecord
    {
        try {
            $this->authorization->authorizeView($context);
        } catch (MedicalRecordAccessDeniedException $exception) {
            $this->auditLogger->logView($tenantId, $patientId, $context->userId, 'denied', $exception->getMessage());

            throw $exception;
        }

        $record = $this->repository->findByPatientId($tenantId, $patientId);

        if ($record === null) {
            $this->auditLogger->logView($tenantId, $patientId, $context->userId, 'denied', 'expediente_no_encontrado');

            throw new MedicalRecordNotFoundException();
        }

        $this->auditLogger->logView($tenantId, $patientId, $context->userId, 'success', null, $record->id);

        return $record;
    }
}
