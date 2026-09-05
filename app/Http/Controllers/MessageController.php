<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Application;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $messages = Message::with(['sender', 'recipient'])
            ->where(fn ($query) => $query->where('sender_id', $userId)->orWhere('recipient_id', $userId))
            ->latest()->get();
        $conversations = $messages->groupBy(fn ($message) => $message->sender_id === $userId ? $message->recipient_id : $message->sender_id)
            ->map(fn ($thread) => $thread->first());
        return view('messages.index', compact('conversations'));
    }

    public function conversation(Request $request, int $user): View
    {
        $userId = $request->user()->id;
        $contact = User::findOrFail($user);
        abort_if($contact->id === $userId || $contact->role === $request->user()->role || $contact->role === 'admin', 403);
        $messages = Message::with(['sender', 'recipient'])
            ->where(fn ($query) => $query->where(fn ($q) => $q->where('sender_id', $userId)->where('recipient_id', $user))->orWhere(fn ($q) => $q->where('sender_id', $user)->where('recipient_id', $userId)))
            ->oldest()->get();
        Message::where('sender_id', $user)->where('recipient_id', $userId)->whereNull('read_at')->update(['read_at' => now()]);
        return view('messages.conversation', compact('messages', 'contact'));
    }

    public function store(StoreMessageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (!empty($data['application_id'])) {
            $application = Application::with('internship')->findOrFail($data['application_id']);
            $participants = [$application->student_id, $application->internship->supervisor_id];
            abort_unless(in_array($request->user()->id, $participants, true) && in_array((int) $data['recipient_id'], $participants, true), 403);
        } else {
            $recipient = User::findOrFail($data['recipient_id']);
            abort_unless(in_array($request->user()->role, ['student', 'supervisor'], true)
                && in_array($recipient->role, ['student', 'supervisor'], true)
                && $request->user()->role !== $recipient->role, 403);
        }

        Message::create([...$data, 'sender_id' => $request->user()->id]);
        return back()->with('status', 'Message sent.');
    }
}