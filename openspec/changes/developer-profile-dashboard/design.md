# Design: Developer Profile & Trends Dashboard Architecture

## System Overview
The Developer Profile dashboard is a Filament admin panel enhancement that aggregates PR analysis data at the developer level. It consists of three main components:

### Component 1: Stats Overview Widget
**Purpose**: Provide executive summary of developer performance  
**Data Source**: Aggregated queries on pr_reports table  
**Metrics**:
- Total Reports: COUNT(id) from pr_reports WHERE developer_id = X
- Avg Tone Score: AVG(tone_score) with color thresholding (red <50, yellow 50-80, green >80)
- Compliance Rate: AVG(solid_compliance_score) with same color logic
- Health Status: Derived from recent reports (if >50% of last 5 are "healthy" → show green, else yellow/red)

**Filament Widget**: Custom Stat widget or multiple built-in Stat cards in a grid  
**Placement**: Header widgets using ViewRecord::getHeaderWidgets()

---

### Component 2: Trend Charts (2-Column Grid)
**Purpose**: Visualize performance progression and detect patterns (burnout, quality decline)

#### Chart A: Technical Quality Trend
- **X-Axis**: PR creation dates or PR number (chronological)
- **Y-Axis**: Two lines (solid_compliance_score, business_value_score)
- **Data**: All reports for this developer, ordered by created_at
- **Interpretation**: Upward trend = improving developer, downward = needs support
- **Library**: ChartJS (via Filament's chart widget or custom Livewire component)
- **Fallback**: "Not enough data to display chart" if <2 data points

#### Chart B: Behavioral Trend
- **X-Axis**: PR creation dates (chronological)
- **Y-Axis**: tone_score (0-100)
- **Data**: All reports, ordered by created_at
- **Interpretation**: Declining tone_score can indicate burnout or frustration
- **Color**: Green if stable/upward, yellow if slightly declining, red if steep decline
- **Fallback**: "No tone data available" if all tone_scores are null/0

**Grid Layout**: 
- Desktop: 2 columns side-by-side
- Tablet: 1 column, stacked
- Mobile: Full width, scrollable

---

### Component 3: PR Reports RelationManager
**Purpose**: Sortable, filterable history of all analyzed PRs  
**Columns**:
1. **Title** → Clickable link to PrReportResource::ViewPrReport page
2. **Risk Level** → Badge with color (red=high, yellow=medium, green=low)
3. **Health Status** → Icon + text (healthy=green check, warning=yellow alert, critical=red X)
4. **Created At** → Formatted date (sortable)

**Filters**:
- Risk Level dropdown (High, Medium, Low, All)
- Date range picker (optional: last 7/30/90 days or custom)

**Sorting**:
- Default: Created At (newest first)
- Allow by: Title, Risk Level, Created At

**Pagination**: 10 records per page

**Actions**:
- Click row → Open PrReportResource::ViewPrReport (the detailed dashboard from Task 2)
- Edit/Delete not applicable (view-only interface)

---

## Data Aggregation Strategy

### Query Structure
```
SELECT AVG(tone_score), AVG(solid_compliance_score), COUNT(*)
FROM pr_reports
WHERE developer_id = :id
```

For charts, fetch all records:
```
SELECT created_at, solid_compliance_score, business_value_score, tone_score
FROM pr_reports
WHERE developer_id = :id
ORDER BY created_at ASC
```

### Caching (Optional Enhancement)
- Stats can be cached for 1 hour to reduce DB hits
- Charts are fresh data (no cache) to show real-time trends

### Color Thresholding
All numeric scores (0-100) use same logic:
- **Red**: < 50 (critical)
- **Yellow**: 50-80 (warning)
- **Green**: ≥ 80 (healthy)

Special case for tone_score:
- Can be calculated as (AVG(reviewer tone_scores))
- If declining (trend line slopes downward): warn for potential burnout
- If all null/zero: skip or show "No feedback yet"

---

## Localization & RTL
- Page title: {{ $developer->name }}'s Profile (or Persian equivalent)
- All stat labels in `lang/fa/developer.php` (new file)
- Chart axis labels translated
- Section headers in Persian with RTL direction (inherited from AdminPanelProvider)
- Fallback messages for empty data

---

## Responsive Behavior

### Desktop (1200px+)
- 2-column chart grid side-by-side
- Stats widget in 4 columns
- Full relation manager table with all columns visible

### Tablet (768px-1199px)
- Stats widget in 2 columns (2x2 grid)
- Charts stack vertically (1 column)
- Relation manager with scrollable columns

### Mobile (<768px)
- Stats widget in 1 column (full width)
- Charts full width, scrollable horizontally
- Relation manager simplified (show Title + Risk Level only, horizontal scroll for others)

---

## Error Handling & Edge Cases

### New Developer (0 reports)
- Stats show "-" or "0"
- Charts display: "Not enough data to display trends"
- Relation manager: "No PR analyses yet. Once PRs are analyzed, they will appear here."

### Developer with 1 report
- Stats show single values
- Charts: "Trend requires at least 2 data points"
- Relation manager: Show single row

### Incomplete data (null scores)
- Tone score null → skip from tone chart, note in fallback
- Compliance/value null → show as 0 or hide from chart
- Health status null → display as "Unknown" badge

---

## Performance Considerations
1. **Indexes**: Ensure `pr_reports(developer_id)` and `pr_reports(created_at)` indexes exist
2. **Query Optimization**: Use `select()` to fetch only needed columns
3. **Pagination**: Limit relation manager to 10-20 rows per page
4. **Chart Data**: If >50 reports, consider sampling data points or aggregating by week
5. **Lazy Loading**: Use Filament's built-in lazy-load for widgets (load charts after stats)

---

## Integration with Existing Components

### Dependency: PrReportResource (Task 2)
- RelationManager links to ViewPrReport page created in Task 2
- Ensures consistent visual language and data presentation

### Dependency: Filament v3.x
- Uses getHeaderWidgets(), RelationManager, Stat widgets
- Requires tailwindcss for responsive grid layout

### Developer Model
- Extends with performance calculation methods (average scores, trend detection)
- Maintains foreign key relationship to pr_reports

