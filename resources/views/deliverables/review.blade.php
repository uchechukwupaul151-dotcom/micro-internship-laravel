@extends('layouts.app')
@section('title', 'Review Submissions | FPNE Micro-Internship')
@section('content')
<main class="container py-5">
    <div class="d-flex justify-content-between align-items-end mb-4"><div><p class="text-uppercase small text-secondary mb-1">Supervisor review</p><h1 class="h2 mb-1">{{ $milestone->title }}</h1><p class="text-secondary mb-0">{{ $milestone->internship->title }} · {{ $milestone->criteria }}</p></div><a class="btn btn-outline-primary" href="{{ route('applications.index', $milestone->internship) }}">Back to project</a></div>
    @forelse($deliverables as $deliverable)
        <article class="card border-0 shadow-sm mb-3"><div class="card-body"><div class="d-flex flex-wrap justify-content-between gap-3"><div><h2 class="h5">{{ $deliverable->student->name }}</h2><p class="small text-secondary mb-1">Submitted {{ $deliverable->submitted_at?->toDateTimeString() }}</p><p class="mb-1">Status: <span class="badge text-bg-light">{{ ucfirst(str_replace('_', ' ', $deliverable->status)) }}</span></p>@if($deliverable->repository_url)<p class="mb-1">Repository: <a href="{{ $deliverable->repository_url }}" target="_blank" rel="noreferrer">{{ $deliverable->repository_url }}</a></p>@endif@if($deliverable->sha256_hash)<p class="small text-secondary mb-0">SHA-256: {{ $deliverable->sha256_hash }}</p>@endif@if($deliverable->commits->isNotEmpty())<div class="mt-3"><strong class="small">Verified commits</strong>@foreach($deliverable->commits as $commit)<div class="small text-secondary mt-1"><a href="{{ $commit->diff }}" target="_blank" rel="noreferrer">{{ substr($commit->commit_hash, 0, 8) }}</a> · {{ $commit->message }} · {{ $commit->committed_at?->toDateString() }}</div>@endforeach</div>@endif</div>@if($deliverable->status === 'pending_verification')<form method="POST" action="{{ route('deliverables.verify', $deliverable) }}">@csrf<button class="btn btn-primary">Run verification</button></form>@endif</div></div></article>
    @empty
        <div class="alert alert-light border">No student submissions yet.</div>
    @endforelse
</main>
@endsection