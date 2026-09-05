@extends('layouts.app')

@section('title', 'Dashboard | FPNE Micro-Internship')

@section('content')
<style>
    .pulse-card { background: linear-gradient(145deg, #102a43, #28666e); color: #fff; }
    .pulse-card h2, .pulse-card p, .pulse-card span { color: #fff !important; }
    .pulse-label { font-family: Arial, sans-serif; font-size: .78rem; letter-spacing: .04em; text-transform: uppercase; opacity: .8; }
    .pulse-track { height: 7px; background: rgba(255,255,255,.18); border-radius: 20px; overflow: hidden; }
    .pulse-fill { height: 100%; border-radius: inherit; }
    .pulse-coral { background: #ef8354; }
    .pulse-gold { background: #f5c76b; }
    .pulse-aqua { background: #66c7c0; }
    .pulse-dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; margin-right: .35rem; }
</style>

<header class="hero-band py-5">
    <div class="container">
        <p class="text-uppercase small mb-2">{{ ucfirst($user->role) }} workspace</p>
        <h1 class="display-6 fw-bold">Welcome back, {{ $user->name }}</h1>
        <p class="lead mb-0">Your micro-internship activity, in one place.</p>
    </div>
</header>

<main class="container py-5">
    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm"><div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4"><h2 class="h4 mb-0">Workspace overview</h2><a class="btn btn-primary" href="{{ route('internships.index') }}">Browse internships</a></div>
                <div class="row g-3">
                    <div class="col-sm-4"><div class="metric bg-light p-3"><div class="text-secondary small">Role</div><strong>{{ ucfirst($user->role) }}</strong></div></div>
                    <div class="col-sm-4"><div class="metric bg-light p-3"><div class="text-secondary small">Account</div><strong>Active</strong></div></div>
                    <div class="col-sm-4"><div class="metric bg-light p-3"><div class="text-secondary small">Notifications</div><a href="{{ route('notifications.index') }}">Open inbox</a></div></div>
                </div>
            </div></div>

            @if($user->role === 'student')
                <div class="card border-0 shadow-sm mt-4"><div class="card-body p-4">
                    <div class="d-flex justify-content-between"><h2 class="h4">Recommended for you</h2><span class="text-secondary">{{ $applications ?? 0 }} applications</span></div>
                    @forelse($recommendations ?? [] as $recommendation)
                        <div class="border-top py-3 d-flex justify-content-between gap-3"><div><h3 class="h6 mb-1">{{ $recommendation['internship']->title }}</h3><p class="small text-secondary mb-0">{{ Str::limit($recommendation['internship']->description, 100) }}</p></div><strong class="text-success">{{ $recommendation['score'] }}%</strong></div>
                    @empty
                        <p class="text-secondary mb-0">Complete your profile to receive recommendations.</p>
                    @endforelse
                </div></div>
            @elseif($user->role === 'supervisor')
                <div class="card border-0 shadow-sm mt-4"><div class="card-body p-4">
                    <h2 class="h4">Your postings</h2>
                    @forelse($internships ?? [] as $internship)
                        <div class="border-top py-3 d-flex justify-content-between"><span>{{ $internship->title }}</span><span class="text-secondary">{{ $internship->applications_count }} applicants</span></div>
                    @empty
                        <p class="text-secondary mb-0">No postings yet.</p>
                    @endforelse
                </div></div>
            @endif
        </div>

        <div class="col-lg-4"><div class="pulse-card rounded-3 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-start mb-4"><div><p class="pulse-label mb-1">Live overview</p><h2 class="h5 mb-0">Activity pulse</h2></div><span class="badge rounded-pill bg-light text-dark">Today</span></div>
            <div class="mb-4"><div class="d-flex justify-content-between mb-2"><span><i class="pulse-dot pulse-coral"></i>Internships</span><strong>{{ $activity['internships'] ?? 0 }}</strong></div><div class="pulse-track"><div class="pulse-fill pulse-coral" style="width: {{ min(100, (($activity['internships'] ?? 0) * 20) + 8) }}%"></div></div></div>
            <div class="mb-4"><div class="d-flex justify-content-between mb-2"><span><i class="pulse-dot pulse-gold"></i>Applications</span><strong>{{ $activity['applications'] ?? 0 }}</strong></div><div class="pulse-track"><div class="pulse-fill pulse-gold" style="width: {{ min(100, (($activity['applications'] ?? 0) * 20) + 8) }}%"></div></div></div>
            <div><div class="d-flex justify-content-between mb-2"><span><i class="pulse-dot pulse-aqua"></i>Accepted</span><strong>{{ $activity['accepted'] ?? 0 }}</strong></div><div class="pulse-track"><div class="pulse-fill pulse-aqua" style="width: {{ min(100, (($activity['accepted'] ?? 0) * 20) + 8) }}%"></div></div></div>
            <p class="small mt-4 mb-0 opacity-75">A quick read on your workspace momentum.</p>
        </div></div>
    </div>
</main>
@endsection