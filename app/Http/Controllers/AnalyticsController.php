<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        return view('analytics.index', ['metrics' => $this->metrics()]);
    }

    public function data(): JsonResponse
    {
        return response()->json($this->metrics());
    }

    private function metrics(): array
    {
        $user = request()->user();
        $internships = Internship::query();

        if ($user->role === 'supervisor') {
            $internships->where('supervisor_id', $user->id);
        }

        $internshipIds = $internships->pluck('id');
        $applications = Application::whereIn('internship_id', $internshipIds);
        if ($user->role === 'student') {
            $applications = Application::where('student_id', $user->id);
        }

        return [
            'internships' => $internships->count(),
            'applications' => (clone $applications)->count(),
            'accepted_applications' => (clone $applications)->where('status', 'accepted')->count(),
            'published_internships' => (clone $internships)->where('status', 'published')->count(),
            'users' => $user->role === 'admin' ? User::count() : null,
        ];
    }
}