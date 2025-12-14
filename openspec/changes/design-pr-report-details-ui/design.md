# Design: Chic PR Report Details UI Architecture

## Visual Layout Structure

```
┌─────────────────────────────────────────────────────────────┐
│                   EXECUTIVE HEADER SECTION                   │
│  [PR Title]                              [View on GitHub]    │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐        │
│  │ Health   │ │ Risk     │ │ Business │ │ Change   │        │
│  │ Healthy  │ │ High     │ │ Value: 85│ │ Feature  │        │
│  │   🟢     │ │   🔴     │ │ ▓▓▓▓░    │ │   📌     │        │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘        │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│              AUTHOR DEEP DIVE SECTION (2-COLUMN)             │
│ ┌───────────────────────┐ ┌───────────────────────┐         │
│ │  QUALITY METRICS      │ │  LEARNING PATH        │         │
│ │  ├─ SOLID: 72/100     │ │  ├─ Recurring Errors: │         │
│ │  ├─ Velocity: High    │ │  │  • Naming Issues   │         │
│ │  └─ Coverage: 85%     │ │  │  • Missing Tests   │         │
│ │                       │ │  ├─ Recommended:      │         │
│ │                       │ │  │  📚 Design Patterns│         │
│ │                       │ │  │  📚 TDD Basics    │         │
│ └───────────────────────┘ └───────────────────────┘         │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│         GAMIFICATION & REVIEWERS SECTION                     │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ BADGES EARNED                                       │    │
│  │ [The Sniper] [Teacher] [Bug Hunter]                │    │
│  └─────────────────────────────────────────────────────┘    │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ TOP REVIEWERS                                       │    │
│  │ ├─ Alice (Tone: 8.5) - Nitpick: 15%               │    │
│  │ └─ Bob (Tone: 7.2) - Nitpick: 25%                 │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│          TECHNICAL DEBT SECTION                              │
│  ⚠️ Over-Engineering Detected                               │
│  ├─ Refactor: Extract Service Layer                        │
│  ├─ Refactor: Simplify Conditional Logic                   │
│  └─ Refactor: Remove Dead Code                             │
└─────────────────────────────────────────────────────────────┘
```

## Component Breakdown

### 1. Executive Header (Section)
**Purpose**: Immediate visual summary of report quality

**Components**:
- **Title & Action**: Large heading with PR title + link button to GitHub/GitLab
- **Status Cards Grid** (4 cards, responsive to 2x2 → 1x4 on mobile):
  - Health Status: Badge with color (green/yellow/red) and icon ♥️
  - Risk Level: Badge with risk icon 🛡️ and severity color
  - Business Value Score: Circular progress + percentage
  - Change Type: Tag-style display (Feature/Bugfix/Refactor/etc)

**Color Logic**:
```
Health Status: healthy=green, warning=yellow, critical=red
Risk Level: low=green, medium=yellow, high=red
Business Value: <50=red, 50-80=yellow, >80=green
Change Type: neutral color (indigo/blue background)
```

**JSON Path**: `raw_analysis.health_status`, `.risk_level`, `.business_value_clarity`, `.change_type`

---

### 2. Author Deep Dive (Section with 2-Column Grid)

**Left Column: Quality Metrics**
- SOLID Compliance Score (with color: <50=red, 50-80=yellow, >80=green)
- Velocity Assessment (extracted from raw_analysis.velocity or similar)
- Test Coverage Percentage (from raw_analysis.test_coverage_percentage)

**Right Column: Learning Path**
- **Recurring Mistakes**: Unordered list
  - Source: `raw_analysis.recurring_mistakes` (array of strings)
  - Fallback: "No recurring mistakes detected" (italic, muted)
- **Educational Recommendations**: Card-based list
  - Source: `raw_analysis.educational_path` (array of {title, reason_fa, url?})
  - Style: Clickable cards with icon, title, and reason text
  - Fallback: "No recommendations at this time"

**JSON Paths**:
```
raw_analysis.solid_compliance_score
raw_analysis.velocity
raw_analysis.test_coverage_percentage
raw_analysis.recurring_mistakes[]
raw_analysis.educational_path[{title, reason_fa, url}]
```

---

### 3. Gamification & Reviewers Section (Split into 2 Subsections)

**Subsection A: Badges Gallery**
- Display all earned badges from `raw_analysis.badges` array
- Each badge shows:
  - Badge name (e.g., "The Sniper", "Teacher")
  - Icon/emoji or color indicator
  - Reason/description in Persian (reason_fa)
- Color scheme: Positive badges (green/gold), negative badges (red/orange)
- Fallback: "No badges yet. Keep improving!"

**Subsection B: Reviewer Statistics**
- Render list of reviewers from `raw_analysis.reviewers_analytics` array
- For each reviewer show:
  - Reviewer name
  - Tone Score (with color: <5=red, 5-7=yellow, >7=green)
  - Nitpicking Ratio (percentage) with brief interpretation
  - Collapsible section with best/worst comment samples
- Fallback: "No reviewer feedback available"

**JSON Paths**:
```
raw_analysis.badges[{name, icon, reason_fa}]
raw_analysis.reviewers_analytics[{reviewer, tone_score, nitpicking_ratio, comments}]
```

---

### 4. Technical Debt Section (Alert + List)

**Over-Engineering Alert**:
- If `raw_analysis.over_engineering === true`, display a warning alert
- Message: "⚠️ This PR shows signs of over-engineering. Consider simplifying the implementation."
- Color: Alert-style (amber/yellow background with dark text)

**Refactoring Suggestions**:
- List from `raw_analysis.suggestions_for_refactor` (array of strings)
- Each item is a bullet point with a code/wrench icon
- Fallback: "No refactoring suggestions at this time. Great job!"

**JSON Paths**:
```
raw_analysis.over_engineering (boolean)
raw_analysis.suggestions_for_refactor[]
```

---

## Filament Infolist Implementation Notes

### Structure Pattern
```php
Infolist::make()
    // Section 1: Executive
    ->schema([
        Section::make('Executive Summary')
            ->columns(4) // 4 cards wide, responsive
            ->schema([...]),
        
        // Section 2: Author Deep Dive
        Section::make('Author Analysis')
            ->columns(2) // 2-column grid
            ->schema([...]),
        
        // Section 3: Gamification
        Section::make('Achievements & Feedback')
            ->schema([
                Tabs::make('Sections')
                    ->tabs([
                        Tabs\Tab::make('Badges')
                            ->schema([...]),
                        Tabs\Tab::make('Reviewers')
                            ->schema([...]),
                    ]),
            ]),
        
        // Section 4: Technical Debt
        Section::make('Technical Debt')
            ->schema([...]),
    ])
```

### Color Coding Helper Functions
Create a custom helper class or use computed properties to determine colors:

```php
public function getHealthColor(): string {
    return match($this->raw_analysis['health_status'] ?? 'unknown') {
        'healthy' => 'success',
        'warning' => 'warning',
        'critical' => 'danger',
        default => 'gray',
    };
}

public function getBusinessValueColor(): string {
    $score = $this->raw_analysis['business_value_clarity'] ?? 0;
    return match(true) {
        $score < 50 => 'danger',
        $score < 80 => 'warning',
        default => 'success',
    };
}
```

### Responsive Grid Behavior
- **Desktop (≥768px)**: `columns(4)` for headers, `columns(2)` for 2-column sections
- **Tablet (640-768px)**: `columns(2)` for everything
- **Mobile (<640px)**: `columns(1)` for all sections (auto-stacking)

Use Filament's `->responsiveHiddenFrom('sm')` or grid col-span modifiers for conditional hiding.

### Null/Empty Handling Strategy
For each subsection:
1. **Check if data exists** (via computed property or accessor)
2. **If empty**: Use `View::make()` with placeholder text or `Hidden::make()` to hide section
3. **If present**: Render normally with proper formatting

Example:
```php
ViewEntry::make('recurring_mistakes')
    ->view('filament.entries.recurring-mistakes')
    ->hidden(fn (PrReport $record) => empty($record->raw_analysis['recurring_mistakes'] ?? [])),
```

---

## RTL & Localization

### RTL Considerations
- All text naturally flows RTL in Filament when locale is set to 'fa'
- Use `direction: rtl` in CSS (already set in AdminPanelProvider)
- Grid/flex layouts auto-adjust (Tailwind respects dir attribute)

### Translation Keys
Create `resources/lang/fa/pr_report.php`:
```php
return [
    'executive_header' => 'خلاصه مدیریتی',
    'health_status' => 'وضعیت سلامت',
    'risk_level' => 'سطح ریسک',
    'business_value' => 'ارزش بیزینسی',
    'author_analysis' => 'تحلیل نویسنده',
    'solid_compliance' => 'رعایت اصول SOLID',
    'no_mistakes' => 'بدون خطای تکراری',
    'badges_earned' => 'نشان‌های کسب شده',
    'technical_debt' => 'بدهی فنی',
    // ... etc
];
```

---

## Performance Considerations

1. **JSON Parsing**: Happens once on model boot via `$casts`
2. **Computed Properties**: Use `#[Computed]` attribute for derived data (PHP 8+)
3. **Avatar/Images**: Lazy load or use CDN-hosted images
4. **Large Data Sets**: 
   - Limit reviewer list display (show top 5, provide "view all" button)
   - Use Collapse component for comment samples
   - Implement pagination if suggestions exceed 10 items

---

## Edge Cases & Fallbacks

| Scenario | Handling |
|----------|----------|
| No badges earned | Hide section or show "No badges yet" message |
| Empty reviewers_analytics | Show "No reviewer feedback available" |
| Missing raw_analysis.over_engineering | Treat as false, don't show alert |
| Educational path has no URL | Display as text-only card (no link) |
| Tone score = null in reviewer | Show "-" or "No data" |
| Repository/PR link invalid | Show read-only text (no clickable link) |

