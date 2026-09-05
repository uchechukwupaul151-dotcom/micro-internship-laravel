<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMilestoneRequest;
use App\Models\Internship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Milestone;
use App\Services\NotificationService;

class MilestoneController extends Controller
{
    public function store(StoreMilestoneRequest $request, Internship $internship, NotificationService $notifications): RedirectResponse
    {
        abort_unless($internship->supervisor_id === $request->user()->id, 403);
        $internship->milestones()->create($request->validated());
        $internship->applications()->where('status', 'accepted')->with('student')->get()->each(fn ($application) => $notifications->send($application->student, 'New milestone added', 'A new milestone was added to ' . $internship->title . ': ' . $request->validated('title') . '.'));

        return back()->with('status', 'Milestone added.');
    }

    public function update(Request $request, Milestone $milestone): RedirectResponse
    {
        abort_unless($milestone->internship->supervisor_id === $request->user()->id, 403);
        $data = $request->validate([
            'sequence' => ['required', 'integer', 'min:1', Rule::unique('milestones')->where(fn ($query) => $query->where('internship_id', $milestone->internship_id))->ignore($milestone->id)],
            'title' => ['required', 'string', 'max:255'], 'deliverable' => ['required', 'string'],
            'criteria' => ['required', 'string'], 'due_date' => ['required', 'date'],
        ]);
        $milestone->update($data);
        return back()->with('status', 'Milestone updated.');
    }

    public function destroy(Request $request, Milestone $milestone): RedirectResponse
    {
        abort_unless($milestone->internship->supervisor_id === $request->user()->id, 403);
        $milestone->delete();
        return back()->with('status', 'Milestone deleted.');
    }
}