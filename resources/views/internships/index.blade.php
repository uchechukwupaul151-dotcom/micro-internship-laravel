@extends('layouts.app')
@section('title', 'Internships | FPNE Micro-Internship')
@section('content')
<main class="container py-5">
    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-end mb-4">
        <div><p class="text-uppercase small text-secondary mb-1">Opportunities</p><h1 class="fw-bold mb-0">Internship opportunities</h1></div>
        @if(auth()->user()->role === 'supervisor')<a href="{{ route('internships.create') }}" class="btn btn-primary">Post internship</a>@endif
    </div>
    <div class="row g-4">
        @forelse($internships as $internship)
            <div class="col-md-6 col-xl-4"><article class="card h-100 border-0 shadow-sm"><div class="card-body p-4">
                <div class="d-flex justify-content-between gap-2 mb-2"><h2 class="h5">{{ $internship->title }}</h2><span class="badge {{ $internship->status === 'published' ? 'text-bg-success' : 'text-bg-warning' }}">{{ ucfirst($internship->status) }}</span></div>
                <p class="text-secondary">{{ Str::limit($internship->description, 150) }}</p><div class="small text-secondary mb-3">{{ $internship->duration_days }} days · {{ number_format((float) $internship->stipend, 2) }} stipend</div>
                @if($internship->status === 'published' && auth()->user()->role === 'student')
                    @if($internship->has_applied)<button class="btn btn-outline-secondary w-100" type="button" disabled>Application submitted</button>@else<form method="POST" action="{{ route('applications.store', $internship) }}">@csrf<textarea name="cover_note" class="form-control mb-2" rows="2" placeholder="Optional cover note"></textarea><button class="btn btn-primary w-100">Apply now</button></form>@endif
                @elseif(auth()->user()->role === 'supervisor' && auth()->id() === $internship->supervisor_id)
                    @if($internship->status !== 'published')<form method="POST" action="{{ route('internships.publish', $internship) }}">@csrf @method('PATCH')<button class="btn btn-primary w-100">Publish and notify students</button></form>@else<a class="btn btn-outline-primary w-100" href="{{ route('applications.index', $internship) }}">Review applicants</a>@endif
                @endif
            </div></article></div>
        @empty
            <div class="col-12"><div class="alert alert-light border">No internship opportunities are available yet.</div></div>
        @endforelse
    </div>
</main>
@endsection