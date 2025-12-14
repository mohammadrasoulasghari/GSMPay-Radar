# Spec: Engagement Leaderboard Widget

## ADDED Requirements

#### Requirement: Engagement Leaderboard Display
The Engagement Leaderboard displays top reviewers ranked by tone score (mentorship quality).

**Scenarios:**
1. Widget displays a list of top 10 reviewers
2. Each row shows: Rank (medal/number), Reviewer Name, Avg Tone Score, Review Count
3. Top 3 are visually highlighted with medal icons (🥇🥈🥉)
4. Tone score color-coded: Green (>8), Yellow (6-8), Red (<6)
5. All text is in Persian
6. Empty state message if no reviewer data exists

**Implementation Notes:**
- Extends `Filament\Widgets\Widget`
- Render as card-based list or table with subtle styling
- Use medals or rank numbers for top positions
- Responsive layout on mobile

---

#### Requirement: Reviewer Metrics Extraction
The widget extracts reviewer analytics from `raw_analysis` across all PRs.

**Scenarios:**
1. Query all PRs from past 30 days
2. For each PR, extract `raw_analysis['reviewers_analytics']` array
3. Each reviewer object contains: `reviewer_name`, `tone_score`, and other review metrics
4. Aggregate by reviewer name:
   - Collect all tone_scores for each reviewer
   - Calculate average tone_score
   - Count number of reviews
5. Sort by average tone_score (descending)
6. Limit to top 10 reviewers

**Implementation Notes:**
- Method: `extractReviewerMetrics(): Collection`
  ```php
  protected function extractReviewerMetrics(): Collection
  {
      return Cache::remember('dashboard:leaderboard', now()->addHour(), function () {
          $reports = PrReport::where('created_at', '>=', now()->subMonth())->get();
          $reviewerData = [];
          
          $reports->each(function ($pr) use (&$reviewerData) {
              $analytics = $pr->raw_analysis['reviewers_analytics'] ?? [];
              foreach ($analytics as $reviewer) {
                  $name = $reviewer['reviewer_name'] ?? 'Unknown';
                  if (!isset($reviewerData[$name])) {
                      $reviewerData[$name] = ['scores' => []];
                  }
                  $reviewerData[$name]['scores'][] = (float)($reviewer['tone_score'] ?? 5);
              }
          });
          
          return collect($reviewerData)
              ->map(fn($data, $name) => [
                  'name' => $name,
                  'avg_tone_score' => round(array_sum($data['scores']) / count($data['scores']), 2),
                  'review_count' => count($data['scores']),
              ])
              ->sortByDesc('avg_tone_score')
              ->take(10)
              ->values();
      });
  }
  ```

---

#### Requirement: Leaderboard Rendering
The widget renders reviewer data in an attractive list format.

**Scenarios:**
1. Display rank (1-10) with medal icons for top 3
2. Display reviewer name (clickable link to developer profile if applicable)
3. Display average tone score with color badge:
   - Green (#10b981): ≥8
   - Yellow (#f59e0b): 6-8
   - Red (#ef4444): <6
4. Display review count as subtitle (e.g., "5 reviews this month")
5. Row styling: Subtle hover effect, light background
6. Responsive layout: Stacks on mobile

**Implementation Notes:**
- Use Blade view or custom HTML rendering
- Rank display: 🥇 🥈 🥉 for positions 1-3, then "4.", "5.", etc.
- Tone score format: "7.8/10" with colored badge background
- Review count as secondary text in smaller, gray font

---

#### Requirement: Leaderboard Cache Strategy
Widget caches reviewer aggregation with 1-hour TTL.

**Scenarios:**
1. Results cached under key `dashboard:leaderboard`
2. Cache duration: 1 hour
3. Dashboard refresh button clears this cache
4. After refresh, widget recalculates from fresh data
5. No queries executed on cache hit

**Implementation Notes:**
- Use `Cache::remember('dashboard:leaderboard', now()->addHour(), function () { ... })`

---

#### Requirement: Leaderboard Translations
All Leaderboard text must be in Persian.

**Scenarios:**
1. Widget title: `__('dashboard.leaderboard')`
2. Column headers:
   - Rank: `__('dashboard.rank')`
   - Reviewer: `__('dashboard.reviewer_name')`
   - Avg Tone Score: `__('dashboard.avg_tone_score')`
   - Review Count: `__('dashboard.review_count')`
3. Empty state: `__('dashboard.leaderboard_empty')` → "No reviewer data available yet"
4. Subtitle text: `__('dashboard.reviews_this_month')` → "reviews this month"

**Implementation Notes:**
- Add keys to `lang/fa/dashboard.php`
- Use `__()` helper in view/widget code
- Tone score unit: "/10" (numeric, no translation)

---

#### Requirement: Tone Score Color Assignment
The widget assigns color to tone score badge based on value ranges.

**Scenarios:**
1. Score ≥8.0 → Green (#10b981) label: `__('dashboard.excellent')`
2. Score 6.0-7.9 → Yellow (#f59e0b) label: `__('dashboard.good')`
3. Score <6.0 → Red (#ef4444) label: `__('dashboard.needs_improvement')`

**Implementation Notes:**
- Method: `getToneScoreColor(float $score): string`
  ```php
  protected function getToneScoreColor(float $score): string
  {
      return match (true) {
          $score >= 8 => '#10b981', // green
          $score >= 6 => '#f59e0b', // yellow
          default => '#ef4444',     // red
      };
  }
  ```
- Use same method in stat card display for consistency

---

#### Requirement: Reviewer Profile Link (Optional Enhancement)
Reviewer names may link to developer profile if a match is found.

**Scenarios:**
1. Extract reviewer_name from raw_analysis
2. Attempt to find matching Developer record by username/name
3. If found, render as link to developer profile page
4. If not found, render as plain text
5. Both cases display equally in leaderboard

**Implementation Notes:**
- This is a nice-to-have for MVP
- Can defer to future update if Developer matching is complex
- Use fuzzy matching or exact match only
- Don't break leaderboard if matching fails
