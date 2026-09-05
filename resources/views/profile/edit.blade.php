@extends('layouts.app')
@section('title', 'Edit profile | FPNE Micro-Internship')
@section('content')
<main class="container py-5">
	<div class="row justify-content-center"><div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-body p-4 p-lg-5">
		<h1 class="h2 mb-4">Edit profile</h1>
		<div class="d-flex align-items-center gap-3 mb-4"><div class="profile-avatar">@if($user->avatar_path)<img src="{{ route('profile.avatar', basename($user->avatar_path)) }}" alt="Profile photo">@else{{ strtoupper(substr($user->name, 0, 1)) }}@endif</div><div><div class="fw-semibold">{{ $user->name }}</div><div class="text-secondary small">{{ ucfirst($user->role) }} account</div></div></div>
		@if($errors->any())
			<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
		@endif
		@if($user->role === 'student')
			<form method="POST" action="{{ route('profile.student.update') }}" enctype="multipart/form-data">
				@csrf @method('PATCH')
				<label class="form-label">Profile photo<input class="form-control" type="file" name="avatar" accept="image/*"></label>@if($user->avatar_path)<label class="form-check mb-2"><input class="form-check-input" type="checkbox" name="remove_avatar" value="1"><span class="form-check-label">Remove current photo</span></label>@endif
				<label class="form-label">Department<input class="form-control" name="department" value="{{ old('department', $user->studentProfile?->department) }}" required></label>
				<label class="form-label">CGPA<input class="form-control" name="cgpa" type="number" step="0.01" min="0" max="5" value="{{ old('cgpa', $user->studentProfile?->cgpa) }}"></label>
				<div class="profile-section mt-4"><div class="d-flex justify-content-between align-items-center mb-2"><div><div class="eyebrow">Payment details</div><h2 class="h5 mb-0">Where your stipend should land</h2></div><span class="profile-section-mark">NGN</span></div><p class="small text-secondary">These details are visible to the supervisor assigned to your internship.</p><div class="row g-3"><div class="col-md-6"><label class="form-label">Bank name<input class="form-control" name="bank_name" value="{{ old('bank_name', $user->studentProfile?->bank_name) }}" placeholder="e.g. First Bank"></label></div><div class="col-md-6"><label class="form-label">Account name<input class="form-control" name="account_name" value="{{ old('account_name', $user->studentProfile?->account_name) }}" placeholder="Name on account"></label></div><div class="col-md-6"><label class="form-label">Account number<input class="form-control" name="account_number" inputmode="numeric" value="{{ old('account_number', $user->studentProfile?->account_number) }}" placeholder="10-digit account number"></label></div></div></div>
				<button class="btn btn-primary mt-2">Update profile</button>
			</form>
		@elseif($user->role === 'supervisor')
			<form method="POST" action="{{ route('profile.supervisor.update') }}" enctype="multipart/form-data">
				@csrf @method('PATCH')
				<label class="form-label">Profile photo<input class="form-control" type="file" name="avatar" accept="image/*"></label>@if($user->avatar_path)<label class="form-check mb-2"><input class="form-check-input" type="checkbox" name="remove_avatar" value="1"><span class="form-check-label">Remove current photo</span></label>@endif
				<label class="form-label">Organization<input class="form-control" name="organization_name" value="{{ old('organization_name', $user->supervisorProfile?->organization_name) }}" required></label>
				<label class="form-label">Contact person<input class="form-control" name="contact_person" value="{{ old('contact_person', $user->supervisorProfile?->contact_person) }}" required></label>
				<label class="form-label">Website<input class="form-control" name="website" value="{{ old('website', $user->supervisorProfile?->website) }}"></label>
				<button class="btn btn-primary mt-2">Update profile</button>
			</form>
		@endif
	</div></div></div></div>
</main>
@endsection