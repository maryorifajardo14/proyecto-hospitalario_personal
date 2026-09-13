<?php

namespace Tests\Feature\MedicalRecord;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Prueba de integración con base de datos real (SQLite en este entorno,
 * esquema compatible con PostgreSQL — ver docs/modulos/mod10/EVIDENCIA.md).
 * Ejercita el flujo HTTP completo: middlewares tenant/jwt.auth, controlador,
 * Application, Domain e Infrastructure (Eloquent) contra la base de datos.
 *
 * Cubre las tres evidencias mínimas del módulo ASII-10: apertura duplicada,
 * consulta autorizada y acceso denegado.
 */
class MedicalRecordApiTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private Patient $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->tenant = Tenant::factory()->create();
        $this->patient = Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    }

    private function tokenFor(User $user): string
    {
        return JWTAuth::fromUser($user);
    }

    private function headersFor(User $user): array
    {
        return [
            'X-Tenant-ID' => $this->tenant->id,
            'Authorization' => 'Bearer '.$this->tokenFor($user),
        ];
    }

    public function test_recepcionista_abre_expediente_y_medico_lo_consulta(): void
    {
        $recepcionista = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $recepcionista->assignRole('Recepcionista');

        $medico = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $medico->assignRole('Médico');

        $openResponse = $this->postJson(
            '/api/v1/medical-records',
            ['patient_id' => $this->patient->id],
            $this->headersFor($recepcionista)
        );

        $openResponse->assertStatus(201);
        $openResponse->assertJsonPath('data.record_number', 'EXP-00001');
        $openResponse->assertJsonPath('data.opened_by', $recepcionista->id);

        $this->assertDatabaseHas('medical_records', [
            'tenant_id' => $this->tenant->id,
            'patient_id' => $this->patient->id,
            'record_number' => 'EXP-00001',
            'opened_by' => $recepcionista->id,
        ]);

        $viewResponse = $this->getJson(
            "/api/v1/medical-records/{$this->patient->id}",
            $this->headersFor($medico)
        );

        $viewResponse->assertStatus(200);
        $viewResponse->assertJsonPath('data.record_number', 'EXP-00001');

        $this->assertDatabaseHas('medical_record_access_logs', [
            'tenant_id' => $this->tenant->id,
            'patient_id' => $this->patient->id,
            'action' => 'view',
            'result' => 'success',
        ]);
    }

    public function test_rechaza_apertura_duplicada_para_el_mismo_paciente(): void
    {
        $recepcionista = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $recepcionista->assignRole('Recepcionista');

        $this->postJson(
            '/api/v1/medical-records',
            ['patient_id' => $this->patient->id],
            $this->headersFor($recepcionista)
        )->assertStatus(201);

        $duplicate = $this->postJson(
            '/api/v1/medical-records',
            ['patient_id' => $this->patient->id],
            $this->headersFor($recepcionista)
        );

        $duplicate->assertStatus(409);

        $this->assertSame(
            1,
            MedicalRecord::query()
                ->where('tenant_id', $this->tenant->id)
                ->where('patient_id', $this->patient->id)
                ->count()
        );
    }

    public function test_deniega_consulta_a_usuario_sin_rol_autorizado(): void
    {
        $recepcionista = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $recepcionista->assignRole('Recepcionista');

        $this->postJson(
            '/api/v1/medical-records',
            ['patient_id' => $this->patient->id],
            $this->headersFor($recepcionista)
        )->assertStatus(201);

        $tecnico = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $tecnico->assignRole('TecnicoLab');

        $response = $this->getJson(
            "/api/v1/medical-records/{$this->patient->id}",
            $this->headersFor($tecnico)
        );

        $response->assertStatus(403);

        $this->assertDatabaseHas('medical_record_access_logs', [
            'tenant_id' => $this->tenant->id,
            'patient_id' => $this->patient->id,
            'action' => 'view',
            'result' => 'denied',
        ]);
    }

    public function test_rechaza_consulta_sin_token(): void
    {
        $response = $this->getJson(
            "/api/v1/medical-records/{$this->patient->id}",
            ['X-Tenant-ID' => $this->tenant->id]
        );

        $response->assertStatus(401);
    }
}
