<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Payment;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::with(['application.internship', 'payer', 'payee'])
            ->where(fn ($query) => $query->where('payer_id', $request->user()->id)->orWhere('payee_id', $request->user()->id))
            ->latest()->get();
        $fundableApplications = $request->user()->role === 'supervisor'
            ? Application::with(['internship', 'student.studentProfile'])
                ->whereHas('internship', fn ($query) => $query->where('supervisor_id', $request->user()->id))
                ->where('status', 'accepted')
                ->whereDoesntHave('payment')
                ->latest()->get()
            : collect();
        return view('payments.index', compact('payments', 'fundableApplications'));
    }

    public function fund(Request $request, Application $application): RedirectResponse
    {
        abort_unless($application->internship->supervisor_id === $request->user()->id && $application->status === 'accepted', 403);
        $amount = $request->validate(['amount' => ['required', 'numeric', 'min:0.01', 'max:100000000']])['amount'];
        Payment::firstOrCreate(['application_id' => $application->id], [
            'payer_id' => $request->user()->id, 'payee_id' => $application->student_id, 'amount' => $amount,
            'reference' => 'ESC-' . Str::upper(Str::random(12)), 'status' => 'funded', 'funded_at' => now(),
        ]);
        return back()->with('status', 'Escrow payment funded.');
    }

    public function release(Request $request, Payment $payment, NotificationService $notifications): RedirectResponse
    {
        abort_unless($payment->payer_id === $request->user()->id && $payment->status === 'funded', 403);
        abort_unless($payment->payee->studentProfile?->bank_name
            && $payment->payee->studentProfile?->account_name
            && $payment->payee->studentProfile?->account_number, 422, 'The student must submit bank details before the transfer can be completed.');
        $payment->update(['status' => 'released', 'released_at' => now()]);
        $notifications->send($payment->payee, 'Stipend released', 'Your stipend for ' . $payment->application->internship->title . ' has been released.');
        return back()->with('status', 'Payment released to the student.');
    }
}