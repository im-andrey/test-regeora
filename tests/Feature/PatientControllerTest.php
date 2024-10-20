<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\PatientRequest;
use App\Jobs\PatientProcess;
use App\Services\PatientService;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Redis;
use Mockery;
use Tests\TestCase;

class PatientControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $patientService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->patientService = Mockery::mock(PatientService::class);
        $this->app->instance(PatientService::class, $this->patientService);
    }

    public function patient_controller_store_method_test()
    {
        $patientData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'birthdate' => '2000-01-01',
        ];

        $this->patientService
            ->shouldReceive('createPatient')
            ->once()
            ->with($patientData)
            ->andReturn((object) $patientData);

        Bus::fake();

        $response = $this->json('POST', '/api/patients', $patientData);

        $response->assertStatus(201);
        $response->assertJson($patientData);

        Redis::shouldReceive('set')
            ->once()
            ->with('patient', (object) $patientData, 'EX', 300);

        Bus::assertDispatched(PatientProcess::class);
    }
}
