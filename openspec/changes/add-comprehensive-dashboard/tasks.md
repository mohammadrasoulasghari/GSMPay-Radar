# Tasks: Comprehensive Team Dashboard Implementation

## Phase 1: Foundation & Team Pulse Widget

### 1.1 Create Dashboard Page Scaffold
- [ ] Create `app/Filament/Pages/Dashboard.php` extending `Filament\Pages\Dashboard`
- [ ] Implement `getWidgets()` method with empty array (will populate in later phases)
- [ ] Add dashboard routing in Filament AdminPanelProvider if needed
- [ ] Test: Dashboard page loads without widgets (blank state)
- [ ] Commit: "feat: scaffold dashboard page for team metrics"

### 1.2 Create TeamPulseStatsWidget
- [ ] Create `app/Filament/Widgets/Dashboard/TeamPulseStatsWidget.php`
- [ ] Extend `Filament\Widgets\StatsOverviewWidget`
- [ ] Implement:
  - `getWeeklyPrCount()`: Query `PrReport::where('created_at', '>=', now()->subWeek())->count()`
  - `getAvgCodeHealth()`: Query `avg(solid_compliance_score)` from past week
  - `getAvgTeamMorale()`: Query `avg(tone_score)` from past week
  - `getCriticalRiskCount()`: Count where `risk_level='high' OR health_status='critical'` in past week
  - `getCachedTeamMetrics()`: Wrap all 4 in `Cache::remember('dashboard:team_pulse', ...)`
- [ ] Implement `getCards(): array` returning 4 `Stat` objects with icons and colors:
  - PRs Analyzed: Blue icon (heroicon-o-chart-bar), primary color
  - Avg Code Health: Green (good) → Yellow (warning) → Red (bad) based on value
  - Team Morale: Green (>7) → Yellow (5-7) → Red (<5)
  - Critical Risks: Red icon, count display
- [ ] Add Persian translations:
  - `dashboard.prs_analyzed_week`, `dashboard.avg_code_health`, `dashboard.team_morale`, `dashboard.critical_risks`
- [ ] Test: Verify all 4 stats display correct values
- [ ] Commit: "feat: add team pulse stats widget with cache"

### 1.3 Register Widget in Dashboard
- [ ] Update `Dashboard::getWidgets()` to include `TeamPulseStatsWidget::class` in full-width grid
- [ ] Test: Widget appears at top of dashboard
- [ ] Verify stats are live/updating
- [ ] Commit: "feat: register team pulse widget on dashboard"

---

## Phase 2: Risk Radar Widget

### 2.1 Create RiskRadarWidget
- [ ] Create `app/Filament/Widgets/Dashboard/RiskRadarWidget.php`
- [ ] Extend `Filament\Widgets\Widget`
- [ ] Implement `getRiskPrs()` query:
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
- [ ] Cache with key `dashboard:risk_radar`, duration 1 hour
- [ ] Implement `getRiskReason(PrReport $pr)` to extract from raw_analysis:
  - Check for `main_risk_factors` array
  - Fallback: first `recurring_mistakes` item
  - Fallback: health_status label

### 2.2 Render Risk Radar Table
- [ ] Create compact table HTML/Blade template or use Filament table columns
- [ ] Columns:
  - **Developer**: Link to developer profile
  - **PR Title**: Link to GitHub/PR view
  - **Risk Reason**: Bold, truncate to 50 chars, tooltip on hover
  - **Created At**: Relative time (e.g., "2 hours ago")
- [ ] Row styling: Highlight high-risk rows in red, critical in darker red
- [ ] Add empty state message: "No critical risks detected ✅"
- [ ] Add refresh button to clear cache and reload

### 2.3 Add Risk Reason Extraction Logic
- [ ] Update `RiskRadarWidget::getRiskReason()` to safely parse `raw_analysis`
- [ ] Handle missing/malformed JSON gracefully
- [ ] Test: Verify risk reason displays sensibly for various PR types
- [ ] Commit: "feat: add risk radar widget with smart risk reason extraction"

### 2.4 Register in Dashboard
- [ ] Update `Dashboard::getWidgets()` to include `RiskRadarWidget::class`
- [ ] Position: 2/3 width on second row (left side)
- [ ] Test: Widget loads and shows recent risky PRs
- [ ] Commit: "feat: register risk radar widget on dashboard"

---

## Phase 3: Skill & Gap Chart Widget

### 3.1 Create SkillGapChartWidget
- [ ] Create `app/Filament/Widgets/Dashboard/SkillGapChartWidget.php`
- [ ] Extend `Filament\Widgets\ChartWidget` (bar chart type)
- [ ] Implement `getChartType()`: return `ChartType::Bar`

### 3.2 Implement Mistake Aggregation Logic
- [ ] Create private method `parseRecurringMistakes(array $rawAnalysis): array`
  - Extract `recurring_mistakes` array from raw_analysis
  - If null/empty, return empty array
  - Otherwise, return array of mistake strings
- [ ] Create private method `categorizeError(string $mistake): string`
  - Keywords for each category:
    - **Testing**: "test", "unit", "integration", "coverage", "mock"
    - **Naming**: "name", "clarity", "variable", "function", "readable"
    - **Documentation**: "comment", "doc", "readme", "explanation", "describe"
    - **Architecture**: "design", "pattern", "structure", "responsibility", "separation"
    - **Performance**: "slow", "optimize", "efficiency", "memory", "query"
    - **Security**: "vulnerability", "injection", "auth", "validation", "encrypt"
  - Use keyword matching (case-insensitive substring search)
  - Fallback: "Other" category
- [ ] Create method `getMistakesAggregation(): array`
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
- [ ] Implement `getChartData(): array`
  - X-axis: Category names
  - Y-axis: Mistake count
  - Colors: Map categories to shades (Testing=blue, Naming=green, etc.)
- [ ] Return Filament chart format:
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
- [ ] Unit test: `parseRecurringMistakes()` with sample JSON
- [ ] Unit test: `categorizeError()` with keywords
- [ ] Integration test: Widget loads with sample data
- [ ] Error handling: Log and display message if JSON parsing fails
- [ ] Commit: "feat: add skill gap chart widget with mistake categorization"

### 3.5 Register in Dashboard
- [ ] Update `Dashboard::getWidgets()` to include `SkillGapChartWidget::class`
- [ ] Position: 1/3 width on second row (right side, next to Risk Radar)
- [ ] Test: Chart displays mistake categories and counts
- [ ] Commit: "feat: register skill gap widget on dashboard"

---

## Phase 4: Engagement Leaderboard Widget

### 4.1 Create EngagementLeaderboardWidget
- [ ] Create `app/Filament/Widgets/Dashboard/EngagementLeaderboardWidget.php`
- [ ] Extend `Filament\Widgets\Widget`

### 4.2 Implement Reviewer Aggregation
- [ ] Create method `extractReviewerMetrics()`:
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
- [ ] Create template/Blade view for top 10 reviewers
- [ ] Columns:
  - **Rank**: 🥇 🥈 🥉 (medals for top 3)
  - **Reviewer Name**: Text
  - **Avg Tone Score**: Colored badge (green >8, yellow 6-8, red <6)
  - **Review Count**: Subtitle (e.g., "5 reviews this month")
  - **Badges**: Display icon badges if reviewer has earned any (future enhancement)
- [ ] Empty state: "No reviewer data yet"
- [ ] Styling: Use cards or list items with subtle hover effect

### 4.4 Add Translations
- [ ] `dashboard.leaderboard` = "جدول نمونه‌های ستاره"
- [ ] `dashboard.reviewer` = "بازبین"
- [ ] `dashboard.avg_tone_score` = "میانگین امتیاز لحن"
- [ ] `dashboard.review_count` = "تعداد بررسی"
- [ ] Commit: "feat: add engagement leaderboard widget"

### 4.5 Register in Dashboard
- [ ] Update `Dashboard::getWidgets()` to include `EngagementLeaderboardWidget::class`
- [ ] Position: Full width on third row (below Risk Radar and Skill Chart)
- [ ] Test: Leaderboard displays top reviewers correctly
- [ ] Commit: "feat: register engagement leaderboard on dashboard"

---

## Phase 5: Dashboard Translations & Polish

### 5.1 Add all dashboard translations to `lang/fa/dashboard.php`
- [ ] Create file `lang/fa/dashboard.php`
- [ ] Add keys:
  - page_title, team_pulse, prs_analyzed_week, avg_code_health, team_morale, critical_risks
  - risk_radar, skill_gap, leaderboard
  - developer_name, pr_title, risk_reason, created_at, reviewer, avg_tone_score, review_count
  - empty_state messages
- [ ] Test: All dashboard text displays in Persian
- [ ] Commit: "feat: add complete Persian translations for dashboard"

### 5.2 Add Polling Support
- [ ] Add `getPollingInterval(): ?string` to each widget (return '30s')
- [ ] Test: Widgets auto-refresh every 30 seconds
- [ ] Optional: Add user toggle to enable/disable polling
- [ ] Commit: "feat: enable 30-second polling on dashboard widgets"

### 5.3 Styling & Polish
- [ ] Ensure consistent spacing, colors match Sky Blue theme (#0ea5e9)
- [ ] Add icons to widget headers (heroicon-o-chart-bar, heroicon-o-exclamation-triangle, etc.)
- [ ] Test responsive layout on mobile (may stack widgets)
- [ ] Verify no console errors or warnings
- [ ] Commit: "style: polish dashboard layout and styling"

### 5.4 Documentation
- [ ] Update `.vscode/copilot_instructions.md` to include dashboard widget guidelines
- [ ] Add inline comments to complex logic (caching, JSON parsing, aggregation)
- [ ] Commit: "docs: update instructions for dashboard maintenance"

---

## Phase 6: Testing & Validation

### 6.1 Unit Tests
- [ ] Test each widget's query logic returns expected data
- [ ] Test cache hit/miss behavior
- [ ] Test error handling (malformed JSON, missing fields)
- [ ] Test categorization logic (mistake→category mapping)

### 6.2 Integration Tests
- [ ] Dashboard page loads without errors
- [ ] All 4 widgets render on dashboard
- [ ] Stats display correct values for sample data
- [ ] Risk Radar shows correct high-risk PRs
- [ ] Skill Chart aggregates mistakes correctly
- [ ] Leaderboard ranks reviewers by tone score

### 6.3 Visual Tests
- [ ] Dashboard layout looks professional
- [ ] All text is in Persian
- [ ] Colors match Sky Blue theme
- [ ] Responsive on mobile/tablet/desktop
- [ ] Widgets have appropriate margins/padding

### 6.4 Performance Tests
- [ ] Dashboard loads in <2 seconds with cache warm
- [ ] Cache invalidation works (refresh button)
- [ ] No N+1 queries
- [ ] Memory usage reasonable (<50MB)
- [ ] Commit: "test: add dashboard widget tests and validations"

---

## Final Validation

### Pre-Merge Checklist
- [ ] All tasks marked complete
- [ ] Code passes lint/formatting checks
- [ ] Tests passing (unit + integration)
- [ ] Dashboard screenshot/demo recorded
- [ ] No TODOs or FIXMEs left
- [ ] Commit message follows convention
- [ ] All 4 widgets on dashboard and functional
- [ ] 100% Persian text (no English in UI)
- [ ] Cache strategy verified working
- [ ] Polling enabled and tested

### Merge
- [ ] Create PR with detailed description
- [ ] Link to this spec + proposal
- [ ] Get code review approval
- [ ] Squash and merge to master
- [ ] Tag release if applicable
