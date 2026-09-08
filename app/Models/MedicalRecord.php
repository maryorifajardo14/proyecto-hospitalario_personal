<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'patient_id',
        'record_number',
        'opened_at',
        'opened_by',
        'background',
        'family_background',
        'surgical_history',
        'obstetric_history',
    ];

    protected $casts = [
        'opened_at' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function vitalSigns()
    {
        return $this->hasMany(VitalSign::class);
    }

    // ASII-10: autor de la apertura del expediente (RN-10-02).
    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    // ASII-10: bitacora de aperturas/consultas de este expediente.
    public function accessLogs()
    {
        return $this->hasMany(MedicalRecordAccessLog::class);
    }
}

