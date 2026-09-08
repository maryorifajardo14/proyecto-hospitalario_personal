<?php

namespace App\Http\Resources;

use App\Domain\MedicalRecord\MedicalRecordRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MedicalRecordRecord
 */
class MedicalRecordResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patientId,
            'record_number' => $this->recordNumber,
            'opened_at' => $this->openedAt->format('Y-m-d'),
            'opened_by' => $this->openedBy,
            'background' => $this->background,
            'family_background' => $this->familyBackground,
            'surgical_history' => $this->surgicalHistory,
            'obstetric_history' => $this->obstetricHistory,
        ];
    }
}
