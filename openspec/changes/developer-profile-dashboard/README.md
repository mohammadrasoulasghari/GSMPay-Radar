# Developer Profile & Trends Dashboard - Change Proposal Summary

## Change ID
`developer-profile-dashboard`

## Overview
This proposal transforms the Developer resource's view page into a comprehensive analytical dashboard that reveals developer performance trends, behavioral patterns, and project history. It enables team leads and managers to quickly assess individual developer productivity, code quality progression, and potential burnout indicators.

## Key Deliverables

### 1. Stats Overview Widget (4 Cards)
- **Total Reports**: Count of analyzed PRs
- **Avg. Tone Score**: Average reviewer tone with color coding (red <50, yellow 50-80, green ≥80)
- **Compliance Rate**: Average solid_compliance_score with same color logic
- **Health Status**: Derived from recent reports (healthy/warning/critical)

### 2. Performance Trend Charts (2-Column Grid)
- **Technical Quality Trend**: Line chart showing solid_compliance_score + business_value_score over time
- **Behavioral Trend**: Line chart showing tone_score trajectory with burnout detection
- Responsive layout: 2 columns desktop → 1 column tablet/mobile
- Fallback messages for <2 data points

### 3. PR Reports RelationManager
- Table with: Title (link), Risk Level (badge), Health Status (icon), Created At (date)
- Sortable by any column (default: Created At descending)
- Filterable by Risk Level (High/Medium/Low/All)
- Paginated (10 per page)
- Click title to navigate to detailed PR report view (Task 2)
- Read-only (no create/edit/delete)

### 4. Developer Model Enhancements
- `getAverageToneScore()` - Average tone_score with null handling
- `getAverageComplianceRate()` - Average solid_compliance_score
- `getTotalReportsCount()` - Count of reports
- `getOverallHealthStatus()` - Derives status from recent 5 reports
- `getTrendData()` - Returns array of [created_at, compliance, tone, value] for charts

### 5. Resource Infrastructure
- `DeveloperResource` Filament resource with List, View, Edit, Create pages
- Dynamic page title with developer name + avatar
- Translation file: `lang/fa/developer.php` with 20+ keys
- Color helper methods for badge/chart coloring

## File Structure
```
openspec/changes/developer-profile-dashboard/
├── proposal.md              (59 lines - summary & motivation)
├── design.md               (173 lines - architecture & system design)
├── tasks.md                (121 lines - 88 ordered implementation tasks)
└── specs/
    ├── developer-resource/spec.md   (85 lines - resource requirements)
    ├── stats-overview/spec.md       (79 lines - stats widget requirements)
    ├── trend-charts/spec.md         (124 lines - chart requirements)
    └── pr-history/spec.md           (140 lines - relation manager requirements)
```

## Dependencies
- **Filament v3.x**: Resource, Infolist, RelationManager, Stat widgets
- **Laravel 11**: Eloquent queries, localization, casting
- **PrReport Model**: Requires solid_compliance_score, business_value_score, tone_score, health_status, risk_level, created_at
- **Developer Model**: Extended with performance calculation methods
- **Task 2 (PR Report Details)**: RelationManager links to ViewPrReport page

## Timeline & Effort
- **Total Tasks**: 88 (organized in 16 phases)
- **Estimated Effort**: 2-3 days of focused development
- **Critical Path**: Phase 1 (Infrastructure) → Phase 2 (Stats) → Phase 3-4 (Charts) → Phase 6 (RelationManager) → Testing

## Success Criteria
1. ✅ Stats widget displays 4 cards with correct colors and values
2. ✅ Both trend charts render with sample data (min 2 points) and show fallbacks for empty data
3. ✅ PR table shows all columns, is sortable/filterable, and links to PR detail page
4. ✅ Developer page is responsive (2-col → 1-col on mobile)
5. ✅ All Persian translations display correctly in RTL mode
6. ✅ Empty state handling for new developers (no crashes)
7. ✅ Navigation flow works: Developers list → Developer detail → PR detail

## Questions for Review
1. Should stats widget use custom Filament Stat cards or HTML grid?
2. Chart library preference: ChartJS (via Filament), Apex Charts, or custom SVG?
3. Should tone score declining warning be automatic alert or user-discovered?
4. Acceptable data point limit for charts (sample large datasets or show all)?

## Next Steps
1. Review proposal, design, and specs
2. Address any questions or concerns
3. Approve change (or request modifications)
4. Begin implementation following tasks.md in sequence
5. Commit changes with detailed commit messages
6. Archive proposal after deployment

---
**Proposal Status**: Ready for Review  
**Date Created**: December 14, 2025  
**Change ID**: `developer-profile-dashboard`
