<?php

namespace App\Http\Controllers;

use App\Models\Deliverable;
use App\Models\GithubCommit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GithubWebhookController extends Controller
{
    public function push(Request $request): JsonResponse
    {
        $secret = (string) config('services.github.webhook_secret', env('GITHUB_WEBHOOK_SECRET', ''));
        $signature = (string) $request->header('X-Hub-Signature-256');
        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);
        abort_unless($secret !== '' && hash_equals($expected, $signature), 401);

        $payload = $request->json()->all();
        $repositoryUrl = data_get($payload, 'repository.html_url');
        $deliverables = Deliverable::where('repository_url', $repositoryUrl)->get();

        foreach ($deliverables as $deliverable) {
            foreach ($payload['commits'] ?? [] as $commit) {
                GithubCommit::updateOrCreate(
                    ['deliverable_id' => $deliverable->id, 'commit_hash' => $commit['id']],
                    [
                        'author_name' => data_get($commit, 'author.name', 'Unknown'),
                        'author_email' => data_get($commit, 'author.email'),
                        'message' => data_get($commit, 'message'),
                        'diff' => data_get($commit, 'url'),
                        'committed_at' => data_get($commit, 'timestamp'),
                    ],
                );
            }
        }

        return response()->json(['stored' => count($payload['commits'] ?? [])]);
    }
}