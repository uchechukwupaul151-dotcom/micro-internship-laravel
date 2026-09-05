@extends('layouts.app')
@section('title', 'Messages | FPNE Micro-Internship')
@section('content')
<main class="container py-5">
    <div class="product-tabs mb-4"><a class="product-tab active" href="{{ route('messages.index') }}"><span class="product-tab-icon">01</span><span><strong>Messages</strong><small>Project conversations</small></span></a><a class="product-tab" href="{{ route('payments.index') }}"><span class="product-tab-icon">02</span><span><strong>Payments</strong><small>Stipend transfers</small></span></a></div>
    <div class="mb-4"><p class="text-uppercase small text-secondary mb-1">Communication hub</p><h1 class="h2 mb-1">Messages</h1><p class="text-secondary mb-0">Keep project conversations close to the work.</p></div>
    <div class="card border-0 shadow-sm"><div class="card-body p-0">
        @forelse($conversations as $conversation)
            @php($contact = $conversation->sender_id === auth()->id() ? $conversation->recipient : $conversation->sender)
            <a class="d-flex align-items-center gap-3 p-4 border-bottom text-decoration-none" href="{{ route('messages.conversation', $contact) }}"><div class="profile-avatar profile-avatar-sm">{{ strtoupper(substr($contact->name, 0, 1)) }}</div><div class="flex-grow-1"><div class="fw-semibold">{{ $contact->name }}</div><div class="small text-secondary text-truncate">{{ $conversation->body }}</div></div><time class="small text-secondary">{{ $conversation->created_at->diffForHumans() }}</time></a>
        @empty
            <div class="p-5 text-center text-secondary">No conversations yet.</div>
        @endforelse
    </div></div>
</main>
@endsection
