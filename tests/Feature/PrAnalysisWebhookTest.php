<?php

namespace Tests\Feature;

use App\Models\Developer;
use App\Models\PrReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrAnalysisWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'repository' => 'example/repo',
            'pr_number' => '42',
            'pr_link' => 'https://github.com/example/repo/pull/42',
            'title' => 'Add new feature',
            'author' => [
                'username' => 'johndoe',
                'name' => 'John Doe',
            ],
            'ai_analysis' => [
                'business_value_clarity' => 85,
                'solid_compliance_score' => 72,
                'health_status' => 'healthy',
                'risk_level' => 'low',
                'change_type' => 'feature',
                'reviewers_analytics' => [
                    ['reviewer' => 'reviewer1', 'tone_score' => 8],
                    ['reviewer' => 'reviewer2', 'tone_score' => 7],
                    ['reviewer' => 'reviewer3', 'tone_score' => 9],
                ],
            ],
        ], $overrides);
    }

    /** @test */
    public function it_creates_developer_and_report_from_valid_payload(): void
    {
        $response = $this->postJson('/api/webhooks/pr-analysis', $this->validPayload());

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'PR analysis report created',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['report_id', 'developer_id', 'repository', 'pr_number'],
            ]);

        $this->assertDatabaseHas('developers', [
            'username' => 'johndoe',
            'name' => 'John Doe',
        ]);

        $this->assertDatabaseHas('pr_reports', [
            'repository' => 'example/repo',
            'pr_number' => '42',
            'business_value_score' => 85,
            'solid_compliance_score' => 72,
            'health_status' => 'healthy',
            'risk_level' => 'low',
            'change_type' => 'feature',
        ]);
    }

    /** @test */
    public function it_calculates_tone_score_as_average_of_reviewers(): void
    {
        $this->postJson('/api/webhooks/pr-analysis', $this->validPayload());

        $report = PrReport::first();

        // (8 + 7 + 9) / 3 = 8.0
        $this->assertEquals(8.00, $report->tone_score);
    }

    /** @test */
    public function it_returns_zero_tone_score_when_no_reviewers(): void
    {
        $payload = $this->validPayload([
            'ai_analysis' => [
                'business_value_clarity' => 50,
                'reviewers_analytics' => [],
            ],
        ]);

        $this->postJson('/api/webhooks/pr-analysis', $payload);

        $report = PrReport::first();
        $this->assertEquals(0.00, $report->tone_score);
    }

    /** @test */
    public function it_updates_developer_name_if_changed(): void
    {
        // First request
        $this->postJson('/api/webhooks/pr-analysis', $this->validPayload());

        // Second request with updated name
        $payload = $this->validPayload([
            'author' => [
                'username' => 'johndoe',
                'name' => 'John D.',
            ],
            'pr_number' => '43',
        ]);
        $this->postJson('/api/webhooks/pr-analysis', $payload);

        $this->assertDatabaseCount('developers', 1);
        $this->assertDatabaseHas('developers', [
            'username' => 'johndoe',
            'name' => 'John D.',
        ]);
    }

    /** @test */
    public function it_does_not_duplicate_developer_on_second_request(): void
    {
        $this->postJson('/api/webhooks/pr-analysis', $this->validPayload());

        $payload = $this->validPayload(['pr_number' => '43']);
        $this->postJson('/api/webhooks/pr-analysis', $payload);

        $this->assertDatabaseCount('developers', 1);
        $this->assertDatabaseCount('pr_reports', 2);
    }

    /** @test */
    public function it_allows_multiple_reports_for_same_pr(): void
    {
        // Submit same PR analysis twice
        $this->postJson('/api/webhooks/pr-analysis', $this->validPayload());
        $this->postJson('/api/webhooks/pr-analysis', $this->validPayload());

        $this->assertDatabaseCount('pr_reports', 2);
    }

    /** @test */
    public function it_returns_422_when_repository_is_missing(): void
    {
        $payload = $this->validPayload();
        unset($payload['repository']);

        $response = $this->postJson('/api/webhooks/pr-analysis', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ])
            ->assertJsonPath('errors.repository.0', 'The repository field is required.');
    }

    /** @test */
    public function it_returns_422_when_author_username_is_missing(): void
    {
        $payload = $this->validPayload();
        unset($payload['author']['username']);

        $response = $this->postJson('/api/webhooks/pr-analysis', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ])
            ->assertJsonStructure(['errors' => ['author.username']]);
    }

    /** @test */
    public function it_returns_422_when_ai_analysis_is_missing(): void
    {
        $payload = $this->validPayload();
        unset($payload['ai_analysis']);

        $response = $this->postJson('/api/webhooks/pr-analysis', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('errors.ai_analysis.0', 'The ai_analysis field is required.');
    }

    /** @test */
    public function it_uses_defaults_when_ai_fields_are_missing(): void
    {
        $payload = $this->validPayload([
            'ai_analysis' => [
                'some_other_field' => 'value', // Has content but missing expected fields
            ],
        ]);

        $response = $this->postJson('/api/webhooks/pr-analysis', $payload);
        $response->assertStatus(201);

        $this->assertDatabaseHas('pr_reports', [
            'business_value_score' => 0,
            'solid_compliance_score' => 0,
            'health_status' => 'unknown',
            'risk_level' => 'unknown',
            'change_type' => 'unknown',
        ]);
    }

    /** @test */
    public function it_stores_raw_analysis_json(): void
    {
        $this->postJson('/api/webhooks/pr-analysis', $this->validPayload());

        $report = PrReport::first();

        $this->assertIsArray($report->raw_analysis);
        $this->assertArrayHasKey('business_value_clarity', $report->raw_analysis);
        $this->assertArrayHasKey('reviewers_analytics', $report->raw_analysis);
    }
}
