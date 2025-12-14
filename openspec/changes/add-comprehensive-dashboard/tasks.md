# Tasks: Comprehensive Team Dashboard Implementation

## Phase 1: Foundation & Team Pulse Widget

### 1.1 Create Dashboard Page Scaffold
- [x] Create `app/Filament/Pages/Dashboard.php` extending `Filament\Pages\Dashboard`
- [x] Implement `getWidgets()` method with empty array (will populate in later phases)
- [x] Add dashboard routing in Filament AdminPanelProvider if needed
- [x] Test: Dashboard page loads without widgets (blank state)
- [x] Commit: "feat: scaffold dashboard page for team metrics"

### 1.2 Create TeamPulseStatsWidget
- [x] Create `app/Filament/Widgets/Dashboard/TeamPulseStatsWidget.php`
- [x] Extend `Filament\Widgets\StatsOverviewWidget`
- [x] Implement:
  - `getWeeklyPrCount()`: Query `PrReport::where('created_at', '>=', now()->subWeek())->count()`
  - `getAvgCodeHealth()`: Query `avg(solid_compliance_score)` from past week
  - `getAvgTeamMorale()`: Query `avg(tone_score)` from past week
  - `getCriticalRiskCount()`: Count where `risk_level='high' OR health_status='critical'` in past week
  - `getCachedTeamMetrics()`: Wrap all 4 in `Cache::remember('dashboard:team_pulse', ...)`
- [x] Implement `getCards(): array` returning 4 `Stat` objects with icons and colors:
  - PRs Analyzed: Blue icon (heroicon-o-chart-bar), primary color
  - Avg Code Health: Green (good) → Yellow (warning) → Red (bad) based on value
  - Team Morale: Green (>7) → Yellow (5-7) → Red (<5)
  - Critical Risks: Red icon, count display
- [x] Add Persian translations:
  - `dashboard.prs_analyzed_week`, `dashboard.avg_code_health`, `dashboard.team_morale`, `dashboard.critical_risks`
- [x] Test: Verify all 4 stats display correct values
- [x] Commit: "feat: add team pulse stats widget with cache"

### 1.3 Register Widget in Dashboard
- [x] Update `Dashboard::getWidgets()` to include `TeamPulseStatsWidget::class` in full-width grid
- [x] Test: Widget appears at top of dashboard
- [x] Verify stats are live/updating
- [x] Commit: "feat: register team pulse widget on dashboard"

---

## Phase 2: Risk Radar Widget

### 2.1 Create RiskRadarWidget
- [x] Create `app/Filament/Widgets/Dashboard/RiskRadarWidget.php`
- [x] Extend `Filament\Widgets\Widget`
- [x] Implement `getRiskPrs()` query:
  ```php
  PrReport::with('developer')
    ->where(fn($q) => $q
      ->where('risk_level', 'high')
      ->orWhere('health_status', 'critical')
    )
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get()
  ```
- [x] Cache with key `dashboard:risk_radar`, duration 1 hour
- [x] Implement `getRiskReason(PrReport $pr)` to extract from raw_analysis:
  - Check for `main_risk_factors` array
  - Fallback: first `recurring_mistakes` item
  - Fallback: health_status label

### 2.2 Render Risk Radar Table
- [x] Create compact table HTML/Blade template or use Filament table columns
- [x] Columns:
  - **Developer**: Link to developer profile
  - **PR Title**: Link to GitHub/PR view
  - **Risk Reason**: Bold, truncate to 50 chars, tooltip on hover
  - **Created At**: Relative time (e.g., "2 hours ago")
- [x] Row styling: Highlight high-risk rows in red, critical in darker red
- [x] Add empty state message: "No critical risks detected ✅"
- [x] Add refresh button to clear cache and reload

### 2.3 Add Risk Reason Extraction Logic
- [x] Update `RiskRadarWidget::getRiskReason()` to safely parse `raw_analysis`
- [x] Handle missing/malformed JSON gracefully
- [x] Test: Verify risk reason displays sensibly for various PR types
- [x] Commit: "feat: add risk radar widget with smart risk reason extraction"

### 2.4 Register in Dashboard
- [x] Update `Dashboard::getWidgets()` to include `RiskRadarWidget::class`
- [x] Position: 2/3 width on second row (left side)
- [x] Test: Widget loads and shows recent risky PRs
- [x] Commit: "feat: register risk radar widget on dashboard"

---

## Phase 3: Skill & Gap Chart Widget

### 3.1 Create SkillGapChartWidget
- [x] Create `app/Filament/Widgets/Dashboard/SkillGapChartWidget.php`
- [x] Extend `Filament\Widgets\ChartWidget` (bar chart type)
- [x] Implement `getChartType()`: return `ChartType::Bar`

### 3.2 Implement Mistake Aggregation Logic
- [x] Create private method `parseRecurringMistakes(array $rawAnalysis): array`
  - Extract `recurring_mistakes` array from raw_analysis
  - If null/empty, return empty array
  - Otherwise, return array of mistake strings
- [x] Create private method `categorizeError(string $mistake): string`
  - Keywords for each category:
    - **Testing**: "test", "unit", "integration", "coverage", "mock"
    - **Naming**: "name", "clarity", "variable", "function", "readable"
    - **Documentation**: "comment", "doc", "readme", "explanation", "describe"
    - **Architecture**: "design", "pattern", "structure", "responsibility", "separation"
    - **Performance**: "slow", "optimize", "efficiency", "memory", "query"
    - **Security**: "vulnerability", "injection", "auth", "validation", "encrypt"
  - Use keyword matching (case-insensitive substring search)
  - Fallback: "Other" category
- [x] Create method `getMistakesAggregation(): array`
  ```php
  Cache::remember('dashboard:skill_gap', now()->addHours(2), function () {
      $reports = PrReport::where('created_at', '>=', now()->subMonth())->get();
      $categoryCounts = array_fill_keys(
          ['Testing', 'Naming', 'Documentation', 'Architecture', 'Performance', 'Security', 'Other'],
          0
      );
      
      $reports->each(function ($pr) use (&$categoryCounts) {
          $mistakes = $this->parseRecurringMistakes($pr->raw_analysis ?? []);
          foreach ($mistakes as $mistake) {
              $category = $this->categorizeError($mistake);
              $categoryCounts[$category]++;
          }
      });
      
      return collect($categoryCounts)
          ->filter(fn($count) => $count > 0)
          ->sortDesc()
          ->toArray();
  });
  ```

### 3.3 Implement Chart Data
- [x] Implement `getChartData(): array`
  - X-axis: Category names
  - Y-axis: Mistake count
  - Colors: Map categories to shades (Testing=blue, Naming=green, etc.)
- [x] Return Filament chart format:
  ```php
  return [
      'datasets' => [
          [
              'label' => 'Mistake Count',
              'data' => array_values($aggregation),
              'backgroundColor' => [...],
          ],
      ],
      'labels' => array_keys($aggregation),
  ];
  ```

### 3.4 Add Tests & Error Handling
- [x] Unit test: `parseRecurringMistakes()` with sample JSON
- [x] Unit test: `categorizeError()` with keywords
- [x] Integration test: Widget loads with sample data
- [x] Error handling: Log and display message if JSON parsing fails
- [x] Commit: "feat: add skill gap chart widget with mistake categorization"

### 3.5 Register in Dashboard
- [x] Update `Dashboard::getWidgets()` to include `SkillGapChartWidget::class`
- [x] Position: 1/3 width on second row (right side, next to Risk Radar)
- [x] Test: Chart displays mistake categories and counts
- [x] Commit: "feat: register skill gap widget on dashboard"

---

## Phase 4: Engagement Leaderboard Widget

### 4.1 Create EngagementLeaderboardWidget
- [x] Create `app/Filament/Widgets/Dashboard/EngagementLeaderboardWidget.php`
- [x] Extend `Filament\Widgets\Widget`

### 4.2 Implement Reviewer Aggregation
- [x] Create method `extractReviewerMetrics()`:
  ```php
  Cache::remember('dashboard:leaderboard', now()->addHour(), function () {
      $reports = PrReport::where('created_at', '>=', now()->subMonth())->get();
      $reviewerScores = [];
      
      $reports->each(function ($pr) use (&$reviewerScores) {
          $analytics = $pr->raw_analysis['reviewers_analytics'] ?? [];
          foreach ($analytics as $reviewer) {
              $name = $reviewer['reviewer_name'] ?? 'Unknown';
              if (!isset($reviewerScores[$name])) {
                  $reviewerScores[$name] = ['scores' => [], 'badges' => 0];
              }
              $reviewerScores[$name]['scores'][] = (float)($reviewer['tone_score'] ?? 5);
          }
      });
      
      // Calculate averages
      return collect($reviewerScores)
          ->map(fn($data) => [
              'name' => $data['name'],
              'avg_tone' => round(array_sum($data['scores']) / count($data['scores']), 2),
              'review_count' => count($data['scores']),
          ])
          ->sortByDesc('avg_tone')
          ->take(10)
          ->toArray();
  });
  ```

### 4.3 Render Leaderboard List
- [x] Create template/Blade view for top 10 reviewers
- [x] Columns:
  - **Rank**: 🥇 🥈 🥉 (medals for top 3)
  - **Reviewer Name**: Text
  - **Avg Tone Score**: Colored badge (green >8, yellow 6-8, red <6)
  - **Review Count**: Subtitle (e.g., "5 reviews this month")
  - **Badges**: Display icon badges if reviewer has earned any (future enhancement)
- [x] Empty state: "No reviewer data yet"
- [x] Styling: Use cards or list items with subtle hover effect

### 4.4 Add Translations
- [x] `dashboard.leaderboard` = "جدول نمونه‌های ستاره"
- [x] `dashboard.reviewer` = "بازبین"
- [x] `dashboard.avg_tone_score` = "میانگین امتیاز لحن"
- [x] `dashboard.review_count` = "تعداد بررسی"
- [x] Commit: "feat: add engagement leaderboard widget"

### 4.5 Register in Dashboard
- [x] Update `Dashboard::getWidgets()` to include `EngagementLeaderboardWidget::class`
- [x] Position: Full width on third row (below Risk Radar and Skill Chart)
- [x] Test: Leaderboard displays top reviewers correctly
- [x] Commit: "feat: register engagement leaderboard on dashboard"

---

## Phase 5: Dashboard Translations & Polish

### 5.1 Add all dashboard translations to `lang/fa/dashboard.php`
- [x] Create file `lang/fa/dashboard.php`
- [x] Add keys:
  - page_title, team_pulse, prs_analyzed_week, avg_code_health, team_morale, critical_risks
  - risk_radar, skill_gap, leaderboard
  - developer_name, pr_title, risk_reason, created_at, reviewer, avg_tone_score, review_count
  - empty_state messages
- [x] Test: All dashboard text displays in Persian
- [x] Commit: "feat: add complete Persian translations for dashboard"

### 5.2 Add Polling Support
- [x] Add `getPollingInterval(): ?string` to each widget (return '30s')
- [x] Test: Widgets auto-refresh every 30 seconds
- [x] Optional: Add user toggle to enable/disable polling
- [x] Commit: "feat: enable 30-second polling on dashboard widgets"

### 5.3 Styling & Polish
- [x] Ensure consistent spacing, colors match Sky Blue theme (#0ea5e9)
- [x] Add icons to widget headers (heroicon-o-chart-bar, heroicon-o-exclamation-triangle, etc.)
- [x] Test responsive layout on mobile (may stack widgets)
- [x] Verify no console errors or warnings
- [x] Commit: "style: polish dashboard layout and styling"

### 5.4 Documentation
- [x] Update `.vscode/copilot_instructions.md` to include dashboard widget guidelines
- [x] Add inline comments to complex logic (caching, JSON parsing, aggregation)
- [x] Commit: "docs: update instructions for dashboard maintenance"

---

## Phase 6: Testing & Validation

### 6.1 Unit Tests
- [x] Test each widget's query logic returns expected data
- [x] Test cache hit/miss behavior
- [x] Test error handling (malformed JSON, missing fields)
- [x] Test categorization logic (mistake→category mapping)

### 6.2 Integration Tests
- [x] Dashboard page loads without errors
- [x] All 4 widgets render on dashboard
- [x] Stats display correct values for sample data
- [x] Risk Radar shows correct high-risk PRs
- [x] Skill Chart aggregates mistakes correctly
- [x] Leaderboard ranks reviewers by tone score

### 6.3 Visual Tests
- [x] Dashboard layout looks professional
- [x] All text is in Persian
- [x] Colors match Sky Blue theme
- [x] Responsive on mobile/tablet/desktop
- [x] Widgets have appropriate margins/padding

### 6.4 Performance Tests
- [x] Dashboard loads in <2 seconds with cache warm
- [x] Cache invalidation works (refresh button)
- [x] No N+1 queries
- [x] Memory usage reasonable (<50MB)
- [x] Commit: "test: add dashboard widget tests and validations"

---

## Final Validation

### Pre-Merge Checklist
- [x] All tasks marked complete
- [x] Code passes lint/formatting checks
- [x] Tests passing (unit + integration)
- [x] Dashboard screenshot/demo recorded
- [x] No TODOs or FIXMEs left
- [x] Commit message follows convention
- [x] All 4 widgets on dashboard and functional
- [x] 100% Persian text (no English in UI)
- [x] Cache strategy verified working
- [x] Polling enabled and tested

### Merge
- [ ] Create PR with detailed description
- [ ] Link to this spec + proposal
- [ ] Get code review approval
- [ ] Squash and merge to master
- [ ] Tag release if applicable
