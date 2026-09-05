<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Internship;
use App\Services\MatchingService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(MatchingService $matching): View
    {
        $user = request()->user();
        $data = ['user' => $user];
        $data['activity'] = [
            'internships' => Internship::where('status', 'published')->count(),
            'applications' => Application::where('student_id', $user->id)->orWhereHas('internship', fn ($query) => $query->where('supervisor_id', $user->id))->count(),
            'accepted' => Application::where('student_id', $user->id)->where('status', 'accepted')->count(),
        ];

        if ($user->role === 'student' && $user->studentProfile) {
            $data['recommendations'] = $matching->rank(
                $user->studentProfile,
                Internship::where('status', 'published')->with('supervisor')->get(),
            );
            $data['applications'] = Application::where('student_id', $user->id)->count();
        } elseif ($user->role === 'supervisor') {
            $data['internships'] = Internship::where('supervisor_id', $user->id)->withCount('applications')->latest()->get();
        }

        return view('dashboard', $data);
    }
}