<?php

namespace App\Providers;

use App\Domain\MedicalRecord\MedicalRecordAuditLogger;
use App\Domain\MedicalRecord\MedicalRecordRepository;
use App\Domain\MedicalRecord\PatientExistenceChecker;
use App\Infrastructure\MedicalRecord\EloquentMedicalRecordAuditLogger;
use App\Infrastructure\MedicalRecord\EloquentMedicalRecordRepository;
use App\Infrastructure\MedicalRecord\EloquentPatientExistenceChecker;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ASII-10 — Expediente médico electrónico base: enlaza los puertos
        // del dominio con sus adaptadores PostgreSQL/Eloquent. Las pruebas
        // de aplicación usan InMemoryMedicalRecordRepository directamente,
        // sin pasar por el contenedor.
        $this->app->bind(MedicalRecordRepository::class, EloquentMedicalRecordRepository::class);
        $this->app->bind(MedicalRecordAuditLogger::class, EloquentMedicalRecordAuditLogger::class);
        $this->app->bind(PatientExistenceChecker::class, EloquentPatientExistenceChecker::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
