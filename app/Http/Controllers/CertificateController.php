<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Application;
use App\Models\Internship;
use App\Services\CertificateService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        return view('certificates.index', ['certificates' => Certificate::with('internship')->where('student_id', $request->user()->id)->latest('issued_at')->get()]);
    }

    public function issue(Request $request, Internship $internship, CertificateService $certificates, NotificationService $notifications): RedirectResponse
    {
        abort_unless($internship->supervisor_id === $request->user()->id, 403);
        $applications = $internship->applications()->with('student')->where('status', 'accepted')->get();
        foreach ($applications as $application) {
            $milestones = $internship->milestones()->count();
            $verified = $internship->milestones()->whereHas('deliverables', fn ($query) => $query->where('student_id', $application->student_id)->where('status', 'verified'))->count();
            if ($milestones > 0 && $verified === $milestones) {
                $certificates->issue($application->student, $internship);
                $notifications->send($application->student, 'Certificate issued', 'Your completion certificate for ' . $internship->title . ' is ready.');
            }
        }
        return back()->with('status', 'Eligible completion certificates issued.');
    }

    public function download(Request $request, Certificate $certificate): Response
    {
        abort_unless($certificate->student_id === $request->user()->id, 403);
        return response()->view('certificates.download', compact('certificate'))->header('Content-Disposition', 'attachment; filename="' . $certificate->certificate_number . '.html"');
    }

    public function verify(string $hash): Response
    {
        $certificate = Certificate::with(['student', 'internship'])->where('verification_hash', $hash)->firstOrFail();

        return response()->view('certificates.verify', compact('certificate'));
    }
}