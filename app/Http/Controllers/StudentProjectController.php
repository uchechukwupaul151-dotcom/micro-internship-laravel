<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentProjectController extends Controller
{
    public function index(): View
    {
        $applications = Application::with([
            'internship.supervisor',
            'internship.milestones',
        ])
            ->with(['internship.milestones.deliverables' => fn ($query) => $query->where('student_id', auth()->id())])
            ->where('student_id', auth()->id())
            ->where('status', 'accepted')
            ->latest()
            ->get();

        return view('student.projects', compact('applications'));
    }

    public function notifySubmission(string $title, NotificationService $notifications): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'student', 403);
        $notifications->send(auth()->user(), 'Milestone submission received', 'Your ' . $title . ' submission is pending supervisor verification.');
        return back();
    }
}