# Spec: Risk Radar Widget

## ADDED Requirements

#### Requirement: Risk Radar Table Display
The Risk Radar widget displays a compact table of recent high-risk PRs.

**Scenarios:**
1. Widget loads and displays up to 10 most recent high-risk PRs
2. Table shows four columns: Developer Name, PR Title, Risk Reason, Created At
3. Rows are color-coded: high-risk (red), critical-health (darker red)
4. Each row links to PR details/GitHub when clicked
5. Empty state message displays when no risky PRs exist
6. All text is in Persian

**Implementation Notes:**
- Extends `Filament\Widgets\Widget` with custom table rendering
- Uses Filament table columns (or Blade view if custom rendering preferred)
- Compact layout: minimal padding, smaller fonts
- Color-coded by `risk_level` and `health_status`

---

#### Requirement: Risk Radar Query
The widget queries and displays PRs matching risk criteria.

**Scenarios:**
1. Query retrieves PRs where: `risk_level='high' OR health_status='critical'`
2. Results sorted by `created_at DESC` (newest first)
3. Limited to 10 rows for dashboard space efficiency
4. Eager-loads `developer` relationship to avoid N+1 queries
5. Filters to past 30 days (optional, configurable)
6. Cache key: `dashboard:risk_radar`, duration 1 hour

**Implementation Notes:**
- Query:
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
- Can add optional date filter: `->where('created_at', '>=', now()->subDays(30))`

---

#### Requirement: Developer Name Column
Display the name of the PR author with link to developer profile.

**Scenarios:**
1. Show developer's full name (or username fallback)
2. Name is clickable link to developer resource page
3. Avatar thumbnail displayed next to name (if available)
4. Fallback avatar generated if `avatar_url` missing

**Implementation Notes:**
- Use `$pr->developer->name ?? $pr->developer->username`
- Link to `route('filament.admin.resources.developers.view', $pr->developer)`

---

#### Requirement: PR Title Column
Display the PR title with link to GitHub or PR details.

**Scenarios:**
1. Show PR title (up to 60 characters, truncate with ellipsis)
2. Title is clickable link to GitHub PR URL (from `pr_link` field)
3. Link opens in new tab
4. Title text is bold for visibility

**Implementation Notes:**
- Text: `$pr->title ?? 'Untitled PR'`
- Link: `$pr->pr_link` (should be valid GitHub URL)
- Use `target="_blank"` for new tab

---

#### Requirement: Risk Reason Column
Display the primary risk factor or reason for high-risk classification.

**Scenarios:**
1. Extract and display best-available risk reason from `raw_analysis`
2. Priority order:
   a. `raw_analysis['main_risk_factors'][0]` if array exists
   b. First item from `raw_analysis['recurring_mistakes']`
   c. Fallback to health_status label (e.g., "Critical Health")
3. Truncate to 50 characters with tooltip showing full text
4. Bold/highlight for emphasis

**Implementation Notes:**
- Method: `getRiskReason(PrReport $pr): string`
  ```php
  protected function getRiskReason(PrReport $pr): string
  {
      $analysis = $pr->raw_analysis ?? [];
      
      // Try main_risk_factors
      if (!empty($analysis['main_risk_factors'])) {
          return $analysis['main_risk_factors'][0];
      }
      
      // Try recurring_mistakes
      if (!empty($analysis['recurring_mistakes'])) {
          return $analysis['recurring_mistakes'][0];
      }
      
      // Fallback
      return __(sprintf('pr_report.%s', $pr->health_status ?? 'critical'));
  }
  ```
- Handle JSON decode errors gracefully, log warnings

---

#### Requirement: Created At Column
Display when the PR was analyzed, using relative time format.

**Scenarios:**
1. Display relative time (e.g., "2 hours ago", "1 day ago")
2. Tooltip shows absolute timestamp (e.g., "Dec 14, 2025 2:30 PM")
3. Uses server timezone
4. Column sortable (click to sort by date)

**Implementation Notes:**
- Use `$pr->created_at->diffForHumans()`
- Tooltip: `$pr->created_at->format('M d, Y g:i A')`

---

#### Requirement: Risk Radar Cache Strategy
Widget caches results with 1-hour TTL and manual refresh capability.

**Scenarios:**
1. Query results cached under key `dashboard:risk_radar`
2. Cache duration: 1 hour
3. Dashboard refresh button clears this cache
4. After refresh, widget refetches data from database
5. No queries execute if cache is valid

**Implementation Notes:**
- Use `Cache::remember('dashboard:risk_radar', now()->addHour(), function () { ... })`

---

#### Requirement: Risk Radar Translations
All Risk Radar labels and messages must be in Persian.

**Scenarios:**
1. Widget title: `__('dashboard.risk_radar')`
2. Column headers: developer_name, pr_title, risk_reason, created_at (all translated)
3. Empty state: `__('dashboard.risk_radar_empty')` → "No critical risks detected ✅"
4. Tooltip on risk reason: "Complete risk details available on PR page"

**Implementation Notes:**
- Add keys to `lang/fa/dashboard.php`
- Use `__()` helper in widget code
