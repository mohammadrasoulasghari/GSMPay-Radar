# Tasks: Developer Profile & Trends Dashboard

## Phase 1: Infrastructure & Model Enhancements
- [x] Create `DeveloperResource` Filament resource class. <!-- task-1 -->
- [x] Create Filament resource pages (ListDevelopers, ViewDeveloper, EditDeveloper). <!-- task-2 -->
- [x] Add performance calculation methods to Developer model (getAverageToneScore, getAverageComplianceRate, etc). <!-- task-3 -->
- [x] Create/register translation keys for Persian labels (resources/lang/fa/developer.php). <!-- task-4 -->
- [x] Ensure pr_reports table has indexes on developer_id and created_at. <!-- task-5 -->

## Phase 2: Stats Overview Widget
- [x] Create custom Stat widget or use Filament's built-in Stat cards (4 cards in a row). <!-- task-6 -->
- [x] Implement Total Reports count display. <!-- task-7 -->
- [x] Implement Average Tone Score with color logic (red <50, yellow 50-80, green >80). <!-- task-8 -->
- [x] Implement Compliance Rate (Average solid_compliance_score) with color logic. <!-- task-9 -->
- [x] Implement Health Status badge (derived from recent reports, color-coded). <!-- task-10 -->
- [x] Add fallback/empty state for new developers with no reports. <!-- task-11 -->
- [x] Position stats widget in getHeaderWidgets() (renders before relation manager). <!-- task-12 -->

## Phase 3: Technical Quality Trend Chart
- [x] Create Line Chart widget displaying solid_compliance_score and business_value_score over time. <!-- task-13 -->
- [x] Fetch and aggregate pr_reports data ordered by created_at (chronological). <!-- task-14 -->
- [x] Implement color coding: green if trending upward, yellow if flat, red if declining. <!-- task-15 -->
- [x] Add X-axis labels (PR dates) and Y-axis labels (score values). <!-- task-16 -->
- [x] Implement fallback message for developers with <2 data points. <!-- task-17 -->
- [x] Ensure chart is responsive (full-width on mobile, half-width on desktop). <!-- task-18 -->

## Phase 4: Behavioral Trend Chart (Tone Score)
- [x] Create Line Chart widget displaying tone_score trajectory over time. <!-- task-19 -->
- [x] Fetch tone_score data from pr_reports, ordered by created_at. <!-- task-20 -->
- [x] Implement color coding: green if stable/upward, yellow if slightly declining, red if steep decline. <!-- task-21 -->
- [x] Add visual indicator or alert if tone_score is trending downward (burnout warning). <!-- task-22 -->
- [x] Implement fallback message if all tone_scores are null/zero. <!-- task-23 -->
- [x] Position alongside Technical Quality chart in 2-column grid (responsive). <!-- task-24 -->

## Phase 5: Chart Integration & Layout
- [x] Organize both charts in a 2-column grid using Filament Section component. <!-- task-25 -->
- [x] Ensure grid collapses to 1 column on tablet/mobile. <!-- task-26 -->
- [x] Add section header "Performance Trends" with Persian translation. <!-- task-27 -->
- [x] Test chart rendering with varying data sizes (1, 5, 10, 50+ reports). <!-- task-28 -->
- [x] Verify responsive behavior across breakpoints. <!-- task-29 -->

## Phase 6: PR Reports RelationManager
- [x] Create `PrReportsRelationManager` class. <!-- task-30 -->
- [x] Implement table with columns: Title (link), Risk Level (badge), Health Status (icon), Created At (date). <!-- task-31 -->
- [x] Make Title column clickable, linking to PrReportResource::ViewPrReport page. <!-- task-32 -->
- [x] Implement Risk Level filter (High, Medium, Low, All). <!-- task-33 -->
- [x] Implement default sorting by Created At (newest first). <!-- task-34 -->
- [x] Add pagination (10 records per page). <!-- task-35 -->
- [x] Set relation manager as read-only (no create/edit/delete actions). <!-- task-36 -->

## Phase 7: Dynamic Page Header & Metadata
- [x] Update ViewDeveloper page to display developer name as title. <!-- task-37 -->
- [x] Add developer avatar display in page header (if avatar_url exists). <!-- task-38 -->
- [x] Show developer username/GitHub handle if available. <!-- task-39 -->
- [x] Add breadcrumb navigation (Developers > Developer Name). <!-- task-40 -->

## Phase 8: Translation & Localization
- [x] Create `resources/lang/fa/developer.php` with all labels. <!-- task-41 -->
- [x] Translate stat widget labels (Total Reports, Avg. Tone Score, Compliance Rate, Health Status). <!-- task-42 -->
- [x] Translate chart section headers and axis labels. <!-- task-43 -->
- [x] Translate table column headers and badge values. <!-- task-44 -->
- [x] Translate empty state messages. <!-- task-45 -->
- [x] Verify all Persian text displays correctly in RTL mode. <!-- task-46 -->

## Phase 9: Developer Model Methods
- [x] Create getAverageToneScore() method with fallback (returns 0 if no reports). <!-- task-47 -->
- [x] Create getAverageComplianceRate() method. <!-- task-48 -->
- [x] Create getTotalReportsCount() method. <!-- task-49 -->
- [x] Create getOverallHealthStatus() method (derives from recent 5 reports, returns enum). <!-- task-50 -->
- [x] Create getTrendData() method returning array of [created_at, compliance, tone, value] for charts. <!-- task-51 -->
- [x] Create getRecentReports(limit=5) for health status calculation. <!-- task-52 -->

## Phase 10: Color Logic & Status Helpers
- [x] Create helper method scoreToColor(int|float) → 'danger'|'warning'|'success'. <!-- task-53 -->
- [x] Create helper method getToneScoreTrend() → detects if tone declining (potential burnout). <!-- task-54 -->
- [x] Create helper method getHealthBadgeColor(string $status) → Filament color map. <!-- task-55 -->
- [x] Create helper method getRiskLevelColor(string $level) → Color for table badges. <!-- task-56 -->

## Phase 11: Null/Empty Data Handling
- [x] Test with new developer (0 reports) — verify no chart crashes, fallback messages display. <!-- task-57 -->
- [x] Test with developer with 1 report — stats show single value, charts show fallback. <!-- task-58 -->
- [x] Test with missing tone_score (all null) — chart handles gracefully. <!-- task-59 -->
- [x] Test with missing compliance_score (all null) — chart and stats handle gracefully. <!-- task-60 -->
- [x] Verify RelationManager shows "No records found" when developer has no reports. <!-- task-61 -->

## Phase 12: Responsive Design & Mobile Testing
- [x] Test stats widget on mobile (should be 1-column, full-width cards). <!-- task-62 -->
- [x] Test charts on mobile (should be full-width, scrollable horizontally if needed). <!-- task-63 -->
- [x] Test chart grid collapse (desktop 2-col → tablet 1-col). <!-- task-64 -->
- [x] Test relation manager table on mobile (key columns visible, horizontal scroll for others). <!-- task-65 -->
- [x] Verify no horizontal overflow on any device. <!-- task-66 -->

## Phase 13: Performance & Query Optimization
- [x] Verify indexes on pr_reports(developer_id) and pr_reports(created_at) exist. <!-- task-67 -->
- [x] Optimize queries: use select() to fetch only needed columns. <!-- task-68 -->
- [ ] Test performance with developer having 100+ reports. <!-- task-69 -->
- [ ] Consider caching stats if needed (optional: 1-hour TTL). <!-- task-70 -->
- [ ] Profile query count to ensure no N+1 problems. <!-- task-71 -->

## Phase 14: Integration & Navigation
- [x] Add DeveloperResource to AdminPanelProvider registration. <!-- task-72 -->
- [x] Verify navigation menu includes Developers link. <!-- task-73 -->
- [x] Test navigation flow: Developers list → Developer detail → PR report detail (Task 2). <!-- task-74 -->
- [x] Ensure back button/breadcrumbs work correctly. <!-- task-75 -->

## Phase 15: Testing & Validation
- [ ] Write feature tests for DeveloperResource pages (index, view). <!-- task-76 -->
- [x] Test that charts render with sample data. <!-- task-77 -->
- [x] Test that RelationManager displays all PR reports for a developer. <!-- task-78 -->
- [x] Test clicking PR title navigates to correct detail page. <!-- task-79 -->
- [x] Test filter functionality (Risk Level filter works). <!-- task-80 -->
- [x] Test sorting (default by created_at, newest first). <!-- task-81 -->
- [x] Test pagination (10 per page). <!-- task-82 -->
- [x] Test empty states (new developer, no reports). <!-- task-83 -->

## Phase 16: Polish & Finalization
- [ ] Review all colors for WCAG AA contrast compliance. <!-- task-84 -->
- [x] Verify all icons are Heroicons for consistency. <!-- task-85 -->
- [x] Final RTL and localization audit. <!-- task-86 -->
- [x] Code cleanup and documentation. <!-- task-87 -->
- [x] Update this tasks.md with completion status. <!-- task-88 -->
