<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Jobs\PatientProcess;
use App\Services\PatientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class PatientController extends Controller
{
    protected $patientService;

    public function __construct (PatientService $patientService)
    {
        $this->patientService = $patientService;
    }
    public function store(PatientRequest $request) {
        $patient = $this->patientService->createPatient($request->validated());

        Redis::set('patient_' . $patient->id, serialize($patient), 'EX', 300);
        PatientProcess::dispatch($patient);

        return response()->json($patient, 201);
    }
}
