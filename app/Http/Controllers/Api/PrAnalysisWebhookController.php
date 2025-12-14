<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrAnalysisRequest;
use App\Models\Developer;
use App\Models\PrReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrAnalysisWebhookController extends Controller
{
    /**
     * Handle incoming PR analysis webhook from n8n.
     *
     * @param StorePrAnalysisRequest $request
     * @return JsonResponse
     */
    public function store(StorePrAnalysisRequest $request): JsonResponse
    {
        try {
            $payload = $request->validated();

            $report = DB::transaction(function () use ($payload) {
                // Step 1: Sync developer (upsert by username)
                $developer = Developer::syncByUsername(
                    username: $payload['author']['username'],
                    name: $payload['author']['name'] ?? null,
                    avatarUrl: $payload['author']['avatar_url'] ?? null
                );

                // Step 2: Create PR report with extracted metrics
                return PrReport::createFromPayload($developer, $payload);
            });

            return response()->json([
                'success' => true,
                'message' => 'PR analysis report created',
                'data' => [
                    'report_id' => $report->id,
                    'developer_id' => $report->developer_id,
                    'repository' => $report->repository,
                    'pr_number' => $report->pr_number,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('PR Analysis Webhook Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }
}
