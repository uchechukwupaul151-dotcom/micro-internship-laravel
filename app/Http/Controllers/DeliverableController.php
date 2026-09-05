<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitDeliverableRequest;
use App\Models\Deliverable;
use App\Models\Milestone;
use App\Services\MilestoneVerificationService;
use Illuminate\Http\RedirectResponse;
use App\Services\NotificationService;
use Illuminate\View\View;

class DeliverableController extends Controller
{
    public function review(Milestone $milestone): View
    {
        abort_unless($milestone->internship->supervisor_id === auth()->id(), 403);

        return view('deliverables.review', [
            'milestone' => $milestone,
            'deliverables' => $milestone->deliverables()->with(['student', 'commits'])->latest('submitted_at')->get(),
        ]);
    }

    public function store(SubmitDeliverableRequest $request, Milestone $milestone, NotificationService $notifications): RedirectResponse
    {
        abort_unless($milestone->internship->applications()->where('student_id', $request->user()->id)->where('status', 'accepted')->exists(), 403);
        $file = $request->file('file');
        $deliverable = Deliverable::updateOrCreate(
            ['milestone_id' => $milestone->id, 'student_id' => $request->user()->id],
            [
                'file_path' => $file?->store('deliverables', 'local'),
                'repository_url' => $request->validated('repository_url'),
                'sha256_hash' => $file ? hash_file('sha256', $file->getRealPath()) : null,
                'status' => 'pending_verification',
                'submitted_at' => now(),
            ],
        );
        $notifications->send($milestone->internship->supervisor, 'Milestone submitted', $request->user()->name . ' submitted work for ' . $milestone->title . '.');
        $notifications->send($request->user(), 'Submission received', 'Your work for ' . $milestone->title . ' is pending verification.');

        return back()->with('status', 'Deliverable submitted for verification.');
    }

    public function verify(MilestoneVerificationService $verification, Deliverable $deliverable, NotificationService $notifications): RedirectResponse
    {
        abort_unless($deliverable->milestone->internship->supervisor_id === auth()->id(), 403);
        $passed = $verification->verify($deliverable);
        $notifications->send($deliverable->student, 'Milestone verification result', 'Your submission for ' . $deliverable->milestone->title . ' was ' . ($passed ? 'verified.' : 'rejected and needs revision.') );

        return back()->with('status', 'Deliverable verification completed.');
    }
}