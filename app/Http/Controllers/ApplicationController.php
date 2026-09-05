<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyInternshipRequest;
use App\Models\Application;
use App\Models\Internship;
use App\Services\MatchingService;
use App\Services\NotificationService;
use App\Http\Requests\UpdateApplicationStatusRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ApplicationController extends Controller
{
    public function index(Internship $internship): View
    {
        abort_unless($internship->supervisor_id === auth()->id(), 403);
        return view('applications.index', [
            'internship' => $internship->load(['milestones', 'applications.student']),
            'applications' => $internship->applications()->with('student')->latest()->get(),
        ]);
    }

    public function store(ApplyInternshipRequest $request, Internship $internship, MatchingService $matching, NotificationService $notifications): RedirectResponse
    {
        abort_unless($internship->status === 'published', 404);
        abort_if(Application::where('internship_id', $internship->id)->where('student_id', $request->user()->id)->exists(), 409, 'You already applied to this internship.');

        Application::create([
            'internship_id' => $internship->id,
            'student_id' => $request->user()->id,
            'cover_note' => $request->validated('cover_note'),
            'match_score' => $matching->score($request->user()->studentProfile, $internship),
        ]);
        $notifications->send($internship->supervisor, 'New internship application', $request->user()->name . ' applied to ' . $internship->title . '.');
        $notifications->send($request->user(), 'Application submitted', 'Your application for ' . $internship->title . ' was submitted successfully.');

        return back()->with('status', 'Application submitted.');
    }

    public function updateStatus(UpdateApplicationStatusRequest $request, Application $application, NotificationService $notifications): RedirectResponse
    {
        abort_unless($application->internship->supervisor_id === $request->user()->id, 403);
        if ($request->validated('status') === 'accepted') {
            $accepted = $application->internship->applications()->where('status', 'accepted')->count();
            abort_if($accepted >= $application->internship->capacity, 409, 'This internship has reached its participant limit.');
        }
        $application->update(['status' => $request->validated('status')]);
        $message = $application->status === 'accepted'
            ? 'You were selected for ' . $application->internship->title . '. You can now submit your milestone work.'
            : 'Your application for ' . $application->internship->title . ' was rejected.';
        $notifications->send($application->student, 'Application status updated', $message);
        return back()->with('status', 'Application status updated.');
    }
}