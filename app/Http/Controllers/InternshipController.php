<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInternshipRequest;
use App\Models\Internship;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InternshipController extends Controller
{
    public function index(): View
    {
        $query = Internship::with('supervisor')->where('status', 'published');
        if (auth()->user()->role === 'supervisor') {
            $query->orWhere('supervisor_id', auth()->id());
        }

        return view('internships.index', ['internships' => $query->withExists(['applications as has_applied' => fn ($query) => $query->where('student_id', auth()->id())])->latest()->get()]);
    }

    public function create(): View
    {
        return view('internships.create');
    }

    public function store(StoreInternshipRequest $request, NotificationService $notifications): RedirectResponse
    {
        $internship = Internship::create([
            ...$request->validated(),
            'supervisor_id' => $request->user()->id,
            'status' => 'published',
        ]);

        $notifications->send($request->user(), 'Internship published', 'Your internship "' . $internship->title . '" is now visible to students.');
        User::where('role', 'student')->each(function (User $student) use ($internship, $notifications): void {
            $notifications->send($student, 'New internship available', $internship->title . ' is now open for applications.');
        });

        return redirect()->route('internships.index')->with('status', 'Internship published and students notified.');
    }

    public function publish(Internship $internship, NotificationService $notifications): RedirectResponse
    {
        abort_unless($internship->supervisor_id === auth()->id(), 403);
        $internship->update(['status' => 'published']);
        $notifications->send(auth()->user(), 'Internship published', 'Your internship "' . $internship->title . '" is now visible to students.');
        User::where('role', 'student')->each(function (User $student) use ($internship, $notifications): void {
            $notifications->send($student, 'New internship available', $internship->title . ' is now open for applications.');
        });

        return back()->with('status', 'Internship published and students notified.');
    }
}