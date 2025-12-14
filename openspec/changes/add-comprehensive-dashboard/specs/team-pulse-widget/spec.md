# Spec: Team Pulse Stats Widget

## ADDED Requirements

#### Requirement: Team Pulse Stats Overview
The Team Pulse widget must display four key team metrics in a stats card layout.

**Scenarios:**
1. Widget loads and displays four stat cards in horizontal layout
2. Each card shows a title, value, and optional change indicator
3. User sees clear color-coding (green=good, yellow=warning, red=critical)
4. Stat values update when new PRs are analyzed
5. All text is in Persian (titles and labels)

**Implementation Notes:**
- Extends `Filament\Widgets\StatsOverviewWidget`
- Four `Stat` objects: PRs Analyzed, Avg Code Health, Team Morale, Critical Risks
- Colors dynamically assigned based on metric value
- Uses cached aggregation queries

---

#### Requirement: PRs Analyzed Metric
Display total count of PRs analyzed in the last 7 days.

**Scenarios:**
1. Query: `PrReport::where('created_at', '>=', now()->subWeek())->count()`
2. Display label: "PRهای تحلیل شده (هفتگی)" (PRs Analyzed - Weekly)
3. Display value: Integer count (e.g., "24")
4. Display icon: `heroicon-o-chart-bar` (blue/info color)
5. Cache key: `dashboard:team_pulse:prs_analyzed`, duration 1 hour

**Implementation Notes:**
- Simple count query, no relationships needed
- Show trend indicator if comparing to previous week (future enhancement)

---

#### Requirement: Average Code Health Metric
Display the average SOLID compliance score across the team for the past week.

**Scenarios:**
1. Query: `PrReport::where('created_at', '>=', now()->subWeek())->avg('solid_compliance_score')`
2. Display label: "میانگین سلامت کد" (Avg Code Health)
3. Display value: Percentage (e.g., "78%")
4. Color coding:
   - ≥80: Green (healthy)
   - 60-79: Yellow (warning)
   - <60: Red (critical)
5. Display icon: `heroicon-o-heart` (colored based on health level)
6. Cache key: `dashboard:team_pulse:code_health`, duration 1 hour

**Implementation Notes:**
- Return float, round to nearest integer for display
- Consider adding decimal (e.g., "78.5%")

---

#### Requirement: Team Morale Metric
Display average tone score from reviewer comments to assess team sentiment.

**Scenarios:**
1. Query: `PrReport::where('created_at', '>=', now()->subWeek())->avg('tone_score')`
2. Display label: "روحیه تیم" (Team Morale)
3. Display value: Score out of 10 (e.g., "7.2/10")
4. Color coding:
   - ≥7: Green (morale healthy)
   - 5-7: Yellow (morale neutral)
   - <5: Red (morale low - burnout risk)
5. Display icon: `heroicon-o-face-smile` (emoji/smiley variant)
6. Cache key: `dashboard:team_pulse:team_morale`, duration 1 hour

**Implementation Notes:**
- Tone score is 0-10 scale
- Used as burnout indicator for HR/management
- Critical for team health assessment

---

#### Requirement: Critical Risks Count
Display count of high-risk or critical-status PRs from the past week.

**Scenarios:**
1. Query: `PrReport::where('created_at', '>=', now()->subWeek())->where(fn($q) => $q->where('risk_level', 'high')->orWhere('health_status', 'critical'))->count()`
2. Display label: "خطرات حادّ" (Critical Risks)
3. Display value: Integer count (e.g., "3")
4. Display icon: `heroicon-o-exclamation-triangle` (red/danger color)
5. Color: Always red/danger (indicating potential issues)
6. Cache key: `dashboard:team_pulse:critical_risks`, duration 1 hour

**Implementation Notes:**
- Count includes either high risk_level OR critical health_status
- Used for quick executive summary
- High count triggers Risk Radar review

---

#### Requirement: Cache Strategy for Team Pulse
All four metrics must use Laravel caching with 1-hour TTL and manual refresh.

**Scenarios:**
1. Widget loads metric from cache if available
2. If cache miss, query executes and result is cached for 1 hour
3. Dashboard has "Refresh" button that clears all cache keys
4. After refresh, metrics recalculate from fresh database query
5. Cache keys follow pattern: `dashboard:team_pulse:*`

**Implementation Notes:**
- Use `Cache::remember('key', duration, callback)`
- Manual refresh deletes keys: `Cache::forget('dashboard:team_pulse:*')`
- Consider warming cache on app boot (future optimization)

---

#### Requirement: Team Pulse Translations
All Team Pulse labels and values must display in Persian.

**Scenarios:**
1. Widget title: `__('dashboard.team_pulse')`
2. Metric label (PRs): `__('dashboard.prs_analyzed_week')`
3. Metric label (Health): `__('dashboard.avg_code_health')`
4. Metric label (Morale): `__('dashboard.team_morale')`
5. Metric label (Risks): `__('dashboard.critical_risks')`
6. All translations in `lang/fa/dashboard.php`

**Implementation Notes:**
- Create `lang/fa/dashboard.php` with all keys
- Use `__()` helper in widget code
- No hardcoded English strings in UI
