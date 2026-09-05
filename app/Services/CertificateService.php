<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Support\Str;

class CertificateService
{
    public function issue(User $student, Internship $internship): Certificate
    {
        $existing = Certificate::where('student_id', $student->id)->where('internship_id', $internship->id)->first();
        if ($existing) {
            return $existing;
        }
        $number = 'FPNE-' . now()->format('Y') . '-' . Str::upper(Str::random(10));
        $hash = hash('sha256', $number . '|' . $student->id . '|' . $internship->id . '|' . config('app.key'));

        return Certificate::create([
            'student_id' => $student->id,
            'internship_id' => $internship->id,
            'certificate_number' => $number,
            'verification_hash' => $hash,
            'issued_at' => now(),
        ]);
    }
}