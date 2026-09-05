<?php

namespace App\Services;

use App\Models\Deliverable;
use App\Models\VerificationLog;
use Illuminate\Validation\ValidationException;

class MilestoneVerificationService
{
    public function verify(Deliverable $deliverable): bool
    {
        if ($deliverable->status !== 'pending_verification') {
            throw ValidationException::withMessages(['status' => 'Only pending deliverables can be verified.']);
        }

        $checks = [
            'has_source' => filled($deliverable->file_path) || filled($deliverable->repository_url),
            'file_hash_recorded' => filled($deliverable->sha256_hash) || blank($deliverable->file_path),
            'github_repository' => blank($deliverable->repository_url) || str_contains(parse_url($deliverable->repository_url, PHP_URL_HOST) ?? '', 'github.com'),
        ];
        $passed = !in_array(false, $checks, true);

        $deliverable->update(['status' => $passed ? 'verified' : 'rejected']);
        VerificationLog::create([
            'deliverable_id' => $deliverable->id,
            'passed' => $passed,
            'checks' => $checks,
        ]);

        return $passed;
    }
}