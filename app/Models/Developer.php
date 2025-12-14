<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Developer extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'name',
        'avatar_url',
    ];

    /**
     * Get all PR reports for this developer.
     */
    public function prReports(): HasMany
    {
        return $this->hasMany(PrReport::class);
    }

    /**
     * Get the total count of PR reports for this developer.
     */
    public function getTotalReportsCount(): int
    {
        return $this->prReports()->count();
    }

    /**
     * Get the average tone score across all PR reports.
     * Returns null if no reports exist.
     */
    public function getAverageToneScore(): ?float
    {
        $avg = $this->prReports()
            ->whereNotNull('tone_score')
            ->where('tone_score', '>', 0)
            ->avg('tone_score');

        return $avg !== null ? round((float) $avg, 2) : null;
    }

    /**
     * Get the average SOLID compliance rate across all PR reports.
     * Returns null if no reports exist.
     */
    public function getAverageComplianceRate(): ?float
    {
        $avg = $this->prReports()
            ->whereNotNull('solid_compliance_score')
            ->avg('solid_compliance_score');

        return $avg !== null ? round((float) $avg, 2) : null;
    }

    /**
     * Get the average business value score across all PR reports.
     * Returns null if no reports exist.
     */
    public function getAverageBusinessValueScore(): ?float
    {
        $avg = $this->prReports()
            ->whereNotNull('business_value_score')
            ->avg('business_value_score');

        return $avg !== null ? round((float) $avg, 2) : null;
    }

    /**
     * Get the most recent N reports for this developer.
     */
    public function getRecentReports(int $limit = 5): Collection
    {
        return $this->prReports()
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Derive overall health status from recent reports.
     * Returns 'healthy', 'warning', 'critical', or 'unknown'.
     */
    public function getOverallHealthStatus(): string
    {
        $recentReports = $this->getRecentReports(5);

        if ($recentReports->isEmpty()) {
            return 'unknown';
        }

        $statusCounts = $recentReports->groupBy('health_status')->map->count();

        // If any critical, return critical
        if (($statusCounts['critical'] ?? 0) > 0) {
            return 'critical';
        }

        $total = $recentReports->count();
        $healthyCount = $statusCounts['healthy'] ?? 0;
        $warningCount = $statusCounts['warning'] ?? 0;

        // If more than 60% healthy
        if ($healthyCount / $total > 0.6) {
            return 'healthy';
        }

        // If more than 60% warning
        if ($warningCount / $total > 0.6) {
            return 'warning';
        }

        // Default to warning for mixed status
        return 'warning';
    }

    /**
     * Get trend data for charts: array of reports with date and metrics.
     * Sorted by created_at ascending (oldest first).
     */
    public function getTrendData(): array
    {
        return $this->prReports()
            ->select(['created_at', 'solid_compliance_score', 'business_value_score', 'tone_score', 'title'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($report) => [
                'date' => $report->created_at->format('M d'),
                'full_date' => $report->created_at->format('Y-m-d'),
                'title' => $report->title,
                'solid_compliance' => (int) ($report->solid_compliance_score ?? 0),
                'business_value' => (int) ($report->business_value_score ?? 0),
                'tone_score' => (float) ($report->tone_score ?? 0),
            ])
            ->toArray();
    }

    /**
     * Detect if tone score is trending downward (potential burnout indicator).
     * Returns: 'stable', 'declining', 'improving', or 'insufficient_data'.
     */
    public function getToneScoreTrend(): string
    {
        $reports = $this->prReports()
            ->whereNotNull('tone_score')
            ->where('tone_score', '>', 0)
            ->orderBy('created_at', 'asc')
            ->pluck('tone_score')
            ->toArray();

        if (count($reports) < 3) {
            return 'insufficient_data';
        }

        // Compare first half average with second half average
        $midpoint = (int) floor(count($reports) / 2);
        $firstHalf = array_slice($reports, 0, $midpoint);
        $secondHalf = array_slice($reports, $midpoint);

        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);

        $diff = $secondAvg - $firstAvg;

        if ($diff < -10) {
            return 'declining'; // Significant decline
        } elseif ($diff > 10) {
            return 'improving'; // Significant improvement
        }

        return 'stable';
    }

    /**
     * Convert a score (0-100) to a Filament color string.
     */
    public static function scoreToColor(?float $score): string
    {
        if ($score === null) {
            return 'gray';
        }

        if ($score < 50) {
            return 'danger';
        } elseif ($score < 80) {
            return 'warning';
        }

        return 'success';
    }

    /**
     * Get Filament color for health status.
     */
    public static function getHealthBadgeColor(string $status): string
    {
        return match ($status) {
            'healthy' => 'success',
            'warning' => 'warning',
            'critical' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get Filament color for risk level.
     */
    public static function getRiskLevelColor(string $level): string
    {
        return match (strtolower($level)) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'success',
            default => 'gray',
        };
    }

    /**
     * Find or create a developer by username, updating name if provided.
     *
     * @param string $username The unique GitHub username
     * @param string|null $name The display name (updated on each call if different)
     * @param string|null $avatarUrl The avatar URL (updated if provided)
     * @return static
     */
    public static function syncByUsername(string $username, ?string $name = null, ?string $avatarUrl = null): static
    {
        $developer = static::firstOrCreate(
            ['username' => $username],
            ['name' => $name, 'avatar_url' => $avatarUrl]
        );

        // Update name if it changed
        $shouldUpdate = false;
        if ($name !== null && $developer->name !== $name) {
            $developer->name = $name;
            $shouldUpdate = true;
        }
        if ($avatarUrl !== null && $developer->avatar_url !== $avatarUrl) {
            $developer->avatar_url = $avatarUrl;
            $shouldUpdate = true;
        }

        if ($shouldUpdate) {
            $developer->save();
        }

        return $developer;
    }
}
