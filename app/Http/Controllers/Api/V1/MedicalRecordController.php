<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\MedicalRecord\OpenMedicalRecordAction;
use App\Application\MedicalRecord\ViewMedicalRecordAction;
use App\Domain\MedicalRecord\AuthorizationContext;
use App\Domain\MedicalRecord\Exceptions\MedicalRecordAccessDeniedException;
use App\Domain\MedicalRecord\Exceptions\MedicalRecordAlreadyExistsException;
use App\Domain\MedicalRecord\Exceptions\MedicalRecordNotFoundException;
use App\Domain\MedicalRecord\Exceptions\PatientNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\OpenMedicalRecordRequest;
use App\Http\Resources\MedicalRecordResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador delgado (Presentation): recibe la solicitud HTTP, delega en la
 * Action correspondiente y traduce la respuesta. No contiene SQL ni decide
 * reglas clinicas/de autorizacion (esas viven en Domain/Application).
 */
class MedicalRecordController extends Controller
{
    public function __construct(
        private readonly OpenMedicalRecordAction $openAction,
        private readonly ViewMedicalRecordAction $viewAction,
    ) {
    }

    /**
     * UC-10-01 — Apertura de expediente.
     */
    public function open(OpenMedicalRecordRequest $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        try {
            $record = $this->openAction->handle(
                (string) $tenant->id,
                (int) $request->validated('patient_id'),
                $this->authorizationContext($request)
            );
        } catch (MedicalRecordAccessDeniedException $exception) {
            return response()->json(['message' => $exception->getMessage()], 403);
        } catch (PatientNotFoundException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        } catch (MedicalRecordAlreadyExistsException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'data' => new MedicalRecordResource($exception->existing),
            ], 409);
        }

        return (new MedicalRecordResource($record))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * UC-10-02 — Consulta longitudinal autorizada. {patient} es el id del
     * paciente, no el id del expediente.
     */
    public function show(Request $request, int $patient): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        try {
            $record = $this->viewAction->handle(
                (string) $tenant->id,
                $patient,
                $this->authorizationContext($request)
            );
        } catch (MedicalRecordAccessDeniedException $exception) {
            return response()->json(['message' => $exception->getMessage()], 403);
        } catch (MedicalRecordNotFoundException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        }

        return (new MedicalRecordResource($record))->response();
    }

    private function authorizationContext(Request $request): AuthorizationContext
    {
        /** @var User $user */
        $user = auth('api')->user();

        return new AuthorizationContext(
            userId: (int) $user->id,
            roles: $user->getRoleNames()->all(),
        );
    }
}
