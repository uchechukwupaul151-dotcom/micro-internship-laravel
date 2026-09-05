@extends('layouts.app')
@section('title', 'Payments | FPNE Micro-Internship')
@section('content')
<main class="container py-5">
    <div class="product-tabs mb-4"><a class="product-tab" href="{{ route('messages.index') }}"><span class="product-tab-icon">01</span><span><strong>Messages</strong><small>Project conversations</small></span></a><a class="product-tab active" href="{{ route('payments.index') }}"><span class="product-tab-icon">02</span><span><strong>Payments</strong><small>Stipend transfers</small></span></a></div>
    <div class="mb-4"><p class="text-uppercase small text-secondary mb-1">Escrow desk</p><h1 class="h2 mb-1">Payments</h1><p class="text-secondary mb-0">Review bank details, fund accepted work, and release stipends.</p></div>
    @if(auth()->user()->role === 'student')
        <div class="payment-callout mb-4"><div><div class="eyebrow">Student action</div><h2 class="h5 mb-1">Add your bank details</h2><p class="mb-0 text-secondary">Your supervisor needs these details before sending your stipend.</p></div><a class="btn btn-primary" href="{{ route('profile.edit') }}">Open profile</a></div>
    @endif
    @if(auth()->user()->role === 'supervisor' && $fundableApplications->isNotEmpty())
        <div class="payment-callout mb-4"><div><div class="eyebrow">Project communication</div><h2 class="h5 mb-1">Talk to your accepted students</h2><p class="mb-0 text-secondary">Keep payment and delivery questions in one place.</p></div><div class="d-flex flex-wrap gap-2">@foreach($fundableApplications as $application)<a class="btn btn-outline-primary btn-sm" href="{{ route('messages.conversation', $application->student) }}">Message {{ $application->student->name }}</a>@endforeach</div></div>
    @endif
    @if(auth()->user()->role === 'supervisor' && $fundableApplications->isNotEmpty())
        <section class="mb-5"><div class="eyebrow mb-2">Ready to fund</div><h2 class="h4 mb-3">Accepted participants</h2><div class="row g-3">
            @foreach($fundableApplications as $application)
                <div class="col-lg-6"><article class="payment-card card border-0 shadow-sm h-100"><div class="card-body p-4"><div class="d-flex justify-content-between gap-3"><div><h3 class="h5 mb-1">{{ $application->internship->title }}</h3><p class="small text-secondary mb-3">{{ $application->student->name }}</p></div><span class="badge status-pending">Awaiting funding</span></div><div class="bank-summary mb-3"><div class="eyebrow">Recipient bank details</div>@if($application->student->studentProfile?->bank_name)<strong>{{ $application->student->studentProfile->bank_name }}</strong><span>{{ $application->student->studentProfile->account_name }} · {{ $application->student->studentProfile->account_number }}</span>@else<span class="text-warning">Student has not submitted bank details yet.</span>@endif</div><form method="POST" action="{{ route('payments.fund', $application) }}" class="d-flex gap-2">@csrf<input class="form-control" type="number" name="amount" min="0.01" step="0.01" placeholder="Amount in NGN" required><button class="btn btn-primary text-nowrap">Fund escrow</button></form></div></article></div>
            @endforeach
        </div></section>
    @endif
    <div class="row g-3">
        @forelse($payments as $payment)
            <div class="col-md-6"><article class="payment-card card border-0 shadow-sm h-100"><div class="card-body p-4"><div class="d-flex justify-content-between gap-3"><h2 class="h5">{{ $payment->application->internship->title }}</h2><span class="badge status-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span></div><p class="display-6 mb-1">{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</p><p class="small text-secondary">Reference {{ $payment->reference }}</p>@if(auth()->id() === $payment->payer_id)<div class="bank-summary mb-3"><div class="eyebrow">Transfer recipient</div><strong>{{ $payment->payee->name }}</strong>@if($payment->payee->studentProfile?->bank_name)<span>{{ $payment->payee->studentProfile->bank_name }} · {{ $payment->payee->studentProfile->account_name }} · {{ $payment->payee->studentProfile->account_number }}</span>@else<span class="text-warning">Bank details not submitted.</span>@endif</div>@endif @if(auth()->id() === $payment->payer_id && $payment->status === 'funded')<form method="POST" action="{{ route('payments.release', $payment) }}">@csrf @method('PATCH')<button class="btn btn-primary btn-sm">Make transfer</button></form>@elseif($payment->status === 'released')<p class="small text-success mb-0">Transfer completed{{ $payment->released_at ? ' · ' . $payment->released_at->format('M j, Y') : '' }}.</p>@endif</div></article></div>
        @empty
            <div class="col-12"><div class="alert alert-light border">No stipend payments recorded yet.</div></div>
        @endforelse
    </div>
</main>
@endsection