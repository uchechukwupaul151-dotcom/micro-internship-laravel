<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateStudentProfileRequest;
use App\Http\Requests\UpdateSupervisorProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit', ['user' => request()->user()->load(['studentProfile', 'supervisorProfile'])]);
    }

    public function avatar(string $filename): StreamedResponse
    {
        $user = request()->user();
        abort_unless($user->avatar_path && basename($user->avatar_path) === $filename, 404);
        abort_unless(Storage::disk('public')->exists($user->avatar_path), 404);
        return Storage::disk('public')->response($user->avatar_path);
    }

    public function updateStudent(UpdateStudentProfileRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['avatar']);
        $this->updateAvatar($request);
        $request->user()->studentProfile()->update($data);
        return back()->with('status', 'Student profile updated.');
    }

    public function updateSupervisor(UpdateSupervisorProfileRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['avatar']);
        $this->updateAvatar($request);
        $request->user()->supervisorProfile()->update($data);
        return back()->with('status', 'Supervisor profile updated.');
    }

    private function updateAvatar($request): void
    {
        $user = $request->user();
        if (($request->boolean('remove_avatar') || $request->hasFile('avatar')) && $user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }
        if ($request->hasFile('avatar')) {
            $user->update(['avatar_path' => $request->file('avatar')->store('avatars', 'public')]);
        }
    }
}