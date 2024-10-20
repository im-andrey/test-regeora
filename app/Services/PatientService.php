<?php

namespace App\Services;

use App\Models\Patient;
use Carbon\Carbon;

class PatientService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createPatient(array $patientData) {
        [$age, $ageType] = $this->calculatePatientAgeAndAgeType($patientData);

        return Patient::create([
            'first_name' => $patientData['first_name'],
            'last_name' => $patientData['last_name'],
            'birthdate' => $patientData['birthdate'],
            'age' => $age,
            'age_type' => $ageType,
        ]);
    }

    protected function calculatePatientAgeAndAgeType($birthDate): array
    {
        $birthday = Carbon::parse($birthDate);
        $now = Carbon::now();

        $age = $now->diffInYears($birthday);
        if ($age > 0) {
            return [$age, 'год'];
        }

        $age = $now->diffInMonths($birthday);
        if ($age > 0) {
            return [$age, 'месяц'];
        }

        $age = $now->diffInDays($birthday);
        return [$age, 'день'];
    }
}
