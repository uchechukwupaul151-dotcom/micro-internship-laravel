<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleInterviewRequest;
use App\Models\Application;
use App\Models\Interview;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InterviewController extends Controller
{
    public function index(Request $request): View
    {
        $interviews = Interview::with(['application.student', 'application.internship'])
            ->whereHas('application', fn ($query) => $query->where('student_id', $request->user()->id)->orWhereHas('internship', fn ($q) => $q->where('supervisor_id', $request->user()->id)))
            ->latest('scheduled_at')->get();
        return view('interviews.index', compact('interviews'));
    }

    public function cancel(Request $request, Interview $interview): RedirectResponse
    {
        $application = $interview->application()->with('internship')->firstOrFail();
        abort_unless($application->student_id === $request->user()->id || $application->internship->supervisor_id === $request->user()->id, 403);
        $interview->update(['status' => 'cancelled']);
        return back()->with('status', 'Interview cancelled.');
    }

    public function store(ScheduleInterviewRequest $request, Application $application, NotificationService $notifications): RedirectResponse
    {
        abort_unless($application->internship->supervisor_id === $request->user()->id && $application->status === 'accepted', 403);
        Interview::create([...$request->validated(), 'application_id' => $application->id, 'scheduled_by' => $request->user()->id]);
        $notifications->send($application->student, 'Interview scheduled', 'An interview has been scheduled for ' . $application->internship->title . '.');
        return back()->with('status', 'Interview scheduled.');
    }
}