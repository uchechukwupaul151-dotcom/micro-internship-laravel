<?php

namespace App\Services;

use App\Models\Internship;
use App\Models\StudentProfile;

class MatchingService
{
    public function score(StudentProfile $student, Internship $internship): float
    {
        $studentSkills = collect($student->technical_skills ?? [])->map(fn ($skill) => strtolower(trim($skill)))->filter();
        $requiredSkills = collect($internship->required_skills ?? [])->map(fn ($skill) => strtolower(trim($skill)))->filter();
        $skillScore = $requiredSkills->isEmpty() ? 0 : $studentSkills->intersect($requiredSkills)->count() / $requiredSkills->count();

        $departmentScore = $student->department && str_contains(strtolower($internship->description), strtolower($student->department)) ? 1 : 0;
        $availabilityScore = empty($student->availability) ? 0 : 1;

        return round(($skillScore * 0.7 + $departmentScore * 0.2 + $availabilityScore * 0.1) * 100, 2);
    }

    public function rank(StudentProfile $student, iterable $internships): array
    {
        $ranked = collect($internships)->map(function (Internship $internship) use ($student) {
            return ['internship' => $internship, 'score' => $this->score($student, $internship)];
        });

        return $ranked->sortByDesc('score')->values()->all();
    }
}