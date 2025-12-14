# Design Document: Comprehensive Team Dashboard

## Architecture Overview

### Component Hierarchy
```
Dashboard (Filament Page)
├── Team Pulse Widget (StatsOverviewWidget)
│   ├── Stat: PRs Analyzed (Week)
│   ├── Stat: Avg Code Health
│   ├── Stat: Team Morale
│   └── Stat: Critical Risks
├── Risk Radar Widget (RiskRadarWidget - Table)
│   └── Recent high-risk PRs with developer, title, risk reason, time
├── Skill & Gap Chart (SkillGapChartWidget)
│   └── Bar chart: Mistake categories and frequency
└── Engagement Leaderboard (EngagementLeaderboardWidget)
    └── List: Top reviewers by tone score with badges
```

### Data Flow

**Team Pulse Widget**:
```
PrReport::query()
  ├─> past 7 days
  ├─> count() → PRs Analyzed
  ├─> avg(solid_compliance_score) → Avg Code Health
  ├─> avg(tone_score) → Team Morale
  └─> count(where risk_level='high' OR health_status='critical') → Critical Risks
```
- **Cache Key**: `dashboard:team_pulse`
- **Duration**: 1 hour
- **Invalidation**: Manual refresh button

**Risk Radar Widget**:
```
PrReport::query()
  ├─> where risk_level='high' OR health_status='critical'
  ├─> orderBy(created_at, desc)
  ├─> limit(10)
  └─> with('developer')
```
- **Columns**: Developer Name, PR Title (link), Risk Reason (from raw_analysis), Created At
- **Cache Key**: `dashboard:risk_radar`
- **Duration**: 1 hour

**Skill & Gap Chart**:
```
PrReport::query()
  ├─> past 30 days (configurable)
  ├─> get()->map(fn($pr) => $pr->raw_analysis['recurring_mistakes'])
  ├─> flatten & count by category
  └─> sort descending
```
- **Categories** (extracted via regex/keyword):
  - Testing (test, unit test, integration)
  - Naming Convention (variable name, function name, clarity)
  - Documentation (comment, doc, readme)
  - Architecture (design, pattern, structure)
  - Performance (slow, optimization, efficiency)
  - Security (vulnerability, injection, auth)
  
- **Cache Key**: `dashboard:skill_gap`
- **Duration**: 2 hours (less frequent updates)

**Engagement Leaderboard**:
```
PrReport::query()
  ├─> past 30 days
  ├─> get()->map(fn($pr) => $pr->raw_analysis['reviewers_analytics'])
  ├─> flatten & group by reviewer name
  ├─> calculate avg(tone_score) per reviewer
  ├─> sort descending
  └─> limit(10)
```
- **Join Data**: Developer model for badge lookup (if badges are stored separately)
- **Cache Key**: `dashboard:leaderboard`
- **Duration**: 1 hour

## Implementation Details

### New Classes to Create

1. **`app/Filament/Widgets/Dashboard/TeamPulseStatsWidget`**
   - Extends: `Filament\Widgets\StatsOverviewWidget`
   - Methods:
     - `protected function getCards(): array` - Returns 4 Stat cards
     - `private function getCachedTeamMetrics(): array` - Cache wrapper
     - `private function getWeeklyPrCount(): int` - Queries past 7 days
     - `private function getAvgCodeHealth(): float` - avg(solid_compliance_score)
     - `private function getAvgTeamMorale(): float` - avg(tone_score)
     - `private function getCriticalRiskCount(): int` - risk_level='high' OR critical status

2. **`app/Filament/Widgets/Dashboard/RiskRadarWidget`**
   - Extends: `Filament\Widgets\Widget` with table-like rendering
   - Methods:
     - `protected function getTableData(): Collection` - Queries risky PRs
     - `protected function renderTable(): string` - Renders compact table HTML
     - Color-codes risk levels (high=red, medium=yellow, low=green)

3. **`app/Filament/Widgets/Dashboard/SkillGapChartWidget`**
   - Extends: `Filament\Widgets\ChartWidget`
   - Methods:
     - `protected function getChartData(): array` - Aggregates mistakes by category
     - `private function parseRecurringMistakes(array $rawAnalysis): array`
     - `private function categorizeError(string $mistake): string` - Keyword-based categorization
     - Returns chart data for Bar/Pie visualization

4. **`app/Filament/Widgets/Dashboard/EngagementLeaderboardWidget`**
   - Extends: `Filament\Widgets\Widget`
   - Methods:
     - `protected function getLeaderboardData(): Collection`
     - `private function extractReviewers(Collection $reports): array`
     - Shows top 10 reviewers with tone score average and badges

5. **`app/Filament/Pages/Dashboard`**
   - Extends: `Filament\Pages\Dashboard`
   - Methods:
     - `public function getWidgets(): array` - Registers all 4 widgets in proper layout
     - Config: Grid layout (top: full width stats, middle: 2/3 + 1/3, bottom: full)

### Cache Strategy

**Cache Layer**: Use Laravel Cache facade with file/Redis driver
- **Keys Pattern**: `dashboard:{widget_name}`
- **Eviction**:
  - Auto: 1–2 hours depending on widget
  - Manual: Dashboard refresh button clears all keys
  - Event-based: Could listen to `PrReport::created` to invalidate (future)

```php
// Example cache call
Cache::remember('dashboard:team_pulse', now()->addHour(), function () {
    return [
        'prs_analyzed' => $this->getWeeklyPrCount(),
        'avg_health' => $this->getAvgCodeHealth(),
        'team_morale' => $this->getAvgTeamMorale(),
        'critical_risks' => $this->getCriticalRiskCount(),
    ];
});
```

### Translations
New keys in `lang/fa/*`:
- `dashboard.page_title` = "داشبورد تیم"
- `dashboard.team_pulse` = "نبض تیم"
- `dashboard.prs_analyzed_week` = "PRهای تحلیل شده (هفتگی)"
- `dashboard.avg_code_health` = "میانگین سلامت کد"
- `dashboard.team_morale` = "روحیه تیم"
- `dashboard.critical_risks` = "خطرات حادّ"
- `dashboard.risk_radar` = "رادار ریسک"
- `dashboard.skill_gap` = "تحلیل مهارت و نیاز"
- `dashboard.leaderboard` = "جدول نمونه‌های ستاره"
- `dashboard.developer_name` = "نام توسعه‌دهنده"
- `dashboard.pr_title` = "عنوان PR"
- `dashboard.risk_reason` = "دلیل ریسک"
- `dashboard.created_at` = "تاریخ ایجاد"
- etc.

### Polling / Live Updates
Each widget can support Filament's polling API:
```php
protected function getPollingInterval(): ?string
{
    return '30s'; // Refresh every 30 seconds
}
```

This requires frontend JS to poll the widget endpoint. Filament handles via Livewire.

### Risk Reason Display Logic
For Risk Radar, display best-effort risk reason:
1. Check `raw_analysis['main_risk_factors']` (if structured)
2. Fallback to first item in `recurring_mistakes`
3. Fallback to `health_status` description

## Performance Considerations

1. **Heavy JSON Parsing**: Skill & Gap aggregation loops over all PRs and parses JSON.
   - *Solution*: Cache for 2 hours, or move to background job later.

2. **Aggregation Queries**: `avg(tone_score)` across reviewers_analytics requires PHP-side grouping.
   - *Solution*: Load PRs in batches, cache intermediate results.

3. **Dashboard Load Time Target**: <2 seconds with cache hits.
   - All stats use `Cache::remember()` with warm-start logic.

## Future Enhancements
- Drill-down views (click Risk Radar row → full PR details)
- Custom date range selection
- Export dashboard snapshot as PDF
- Webhook-triggered cache invalidation
- Separate Reviewer model to avoid raw_analysis parsing
- Background job for aggregation (defer heavy work)
