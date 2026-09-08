<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Solo valida formato basico (RN-10-03/RN-10-04 y la existencia real del
 * paciente son responsabilidad del dominio/aplicacion, no de esta capa).
 */
class OpenMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La autorizacion fina la decide MedicalRecordAuthorizationChecker
        // dentro de OpenMedicalRecordAction; este FormRequest solo valida
        // formato de entrada, tal como exige la separacion de capas del
        // modulo (ver docs/modulos/mod10/ADR-001-arquitectura.md).
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'min:1'],
        ];
    }
}
