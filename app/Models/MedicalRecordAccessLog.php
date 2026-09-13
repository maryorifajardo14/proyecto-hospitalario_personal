<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Bitacora HOSPITAL local de aperturas/consultas del expediente (ASII-10).
 * Es de solo adicion desde la aplicacion: no se expone ninguna operacion de
 * actualizacion ni borrado via API (equivalente a RNF-10-04 documentada en
 * docs/module-10/semana-2-rf-rnf-solid.md).
 */
class MedicalRecordAccessLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id',
        'medical_record_id',
        'patient_id',
        'user_id',
        'action',
        'result',
        'reason',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
