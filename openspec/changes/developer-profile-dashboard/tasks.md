# Tasks: Developer Profile & Trends Dashboard

## Phase 1: Infrastructure & Model Enhancements
- [ ] Create `DeveloperResource` Filament resource class. <!-- task-1 -->
- [ ] Create Filament resource pages (ListDevelopers, ViewDeveloper, EditDeveloper). <!-- task-2 -->
- [ ] Add performance calculation methods to Developer model (getAverageToneScore, getAverageComplianceRate, etc). <!-- task-3 -->
- [ ] Create/register translation keys for Persian labels (resources/lang/fa/developer.php). <!-- task-4 -->
- [ ] Ensure pr_reports table has indexes on developer_id and created_at. <!-- task-5 -->

## Phase 2: Stats Overview Widget
- [ ] Create custom Stat widget or use Filament's built-in Stat cards (4 cards in a row). <!-- task-6 -->
- [ ] Implement Total Reports count display. <!-- task-7 -->
- [ ] Implement Average Tone Score with color logic (red <50, yellow 50-80, green >80). <!-- task-8 -->
- [ ] Implement Compliance Rate (Average solid_compliance_score) with color logic. <!-- task-9 -->
- [ ] Implement Health Status badge (derived from recent reports, color-coded). <!-- task-10 -->
- [ ] Add fallback/empty state for new developers with no reports. <!-- task-11 -->
- [ ] Position stats widget in getHeaderWidgets() (renders before relation manager). <!-- task-12 -->

## Phase 3: Technical Quality Trend Chart
- [ ] Create Line Chart widget displaying solid_compliance_score and business_value_score over time. <!-- task-13 -->
- [ ] Fetch and aggregate pr_reports data ordered by created_at (chronological). <!-- task-14 -->
- [ ] Implement color coding: green if trending upward, yellow if flat, red if declining. <!-- task-15 -->
- [ ] Add X-axis labels (PR dates) and Y-axis labels (score values). <!-- task-16 -->
- [ ] Implement fallback message for developers with <2 data points. <!-- task-17 -->
- [ ] Ensure chart is responsive (full-width on mobile, half-width on desktop). <!-- task-18 -->

## Phase 4: Behavioral Trend Chart (Tone Score)
- [ ] Create Line Chart widget displaying tone_score trajectory over time. <!-- task-19 -->
- [ ] Fetch tone_score data from pr_reports, ordered by created_at. <!-- task-20 -->
- [ ] Implement color coding: green if stable/upward, yellow if slightly declining, red if steep decline. <!-- task-21 -->
- [ ] Add visual indicator or alert if tone_score is trending downward (burnout warning). <!-- task-22 -->
- [ ] Implement fallback message if all tone_scores are null/zero. <!-- task-23 -->
- [ ] Position alongside Technical Quality chart in 2-column grid (responsive). <!-- task-24 -->

## Phase 5: Chart Integration & Layout
- [ ] Organize both charts in a 2-column grid using Filament Section component. <!-- task-25 -->
- [ ] Ensure grid collapses to 1 column on tablet/mobile. <!-- task-26 -->
- [ ] Add section header "Performance Trends" with Persian translation. <!-- task-27 -->
- [ ] Test chart rendering with varying data sizes (1, 5, 10, 50+ reports). <!-- task-28 -->
- [ ] Verify responsive behavior across breakpoints. <!-- task-29 -->

## Phase 6: PR Reports RelationManager
- [ ] Create `PrReportsRelationManager` class. <!-- task-30 -->
- [ ] Implement table with columns: Title (link), Risk Level (badge), Health Status (icon), Created At (date). <!-- task-31 -->
- [ ] Make Title column clickable, linking to PrReportResource::ViewPrReport page. <!-- task-32 -->
- [ ] Implement Risk Level filter (High, Medium, Low, All). <!-- task-33 -->
- [ ] Implement default sorting by Created At (newest first). <!-- task-34 -->
- [ ] Add pagination (10 records per page). <!-- task-35 -->
- [ ] Set relation manager as read-only (no create/edit/delete actions). <!-- task-36 -->

## Phase 7: Dynamic Page Header & Metadata
- [ ] Update ViewDeveloper page to display developer name as title. <!-- task-37 -->
- [ ] Add developer avatar display in page header (if avatar_url exists). <!-- task-38 -->
- [ ] Show developer username/GitHub handle if available. <!-- task-39 -->
- [ ] Add breadcrumb navigation (Developers > Developer Name). <!-- task-40 -->

## Phase 8: Translation & Localization
- [ ] Create `resources/lang/fa/developer.php` with all labels. <!-- task-41 -->
- [ ] Translate stat widget labels (Total Reports, Avg. Tone Score, Compliance Rate, Health Status). <!-- task-42 -->
- [ ] Translate chart section headers and axis labels. <!-- task-43 -->
- [ ] Translate table column headers and badge values. <!-- task-44 -->
- [ ] Translate empty state messages. <!-- task-45 -->
- [ ] Verify all Persian text displays correctly in RTL mode. <!-- task-46 -->

## Phase 9: Developer Model Methods
- [ ] Create getAverageToneScore() method with fallback (returns 0 if no reports). <!-- task-47 -->
- [ ] Create getAverageComplianceRate() method. <!-- task-48 -->
- [ ] Create getTotalReportsCount() method. <!-- task-49 -->
- [ ] Create getOverallHealthStatus() method (derives from recent 5 reports, returns enum). <!-- task-50 -->
- [ ] Create getTrendData() method returning array of [created_at, compliance, tone, value] for charts. <!-- task-51 -->
- [ ] Create getRecentReports(limit=5) for health status calculation. <!-- task-52 -->

## Phase 10: Color Logic & Status Helpers
- [ ] Create helper method scoreToColor(int|float) → 'danger'|'warning'|'success'. <!-- task-53 -->
- [ ] Create helper method getToneScoreTrend() → detects if tone declining (potential burnout). <!-- task-54 -->
- [ ] Create helper method getHealthBadgeColor(string $status) → Filament color map. <!-- task-55 -->
- [ ] Create helper method getRiskLevelColor(string $level) → Color for table badges. <!-- task-56 -->

## Phase 11: Null/Empty Data Handling
- [ ] Test with new developer (0 reports) — verify no chart crashes, fallback messages display. <!-- task-57 -->
- [ ] Test with developer with 1 report — stats show single value, charts show fallback. <!-- task-58 -->
- [ ] Test with missing tone_score (all null) — chart handles gracefully. <!-- task-59 -->
- [ ] Test with missing compliance_score (all null) — chart and stats handle gracefully. <!-- task-60 -->
- [ ] Verify RelationManager shows "No records found" when developer has no reports. <!-- task-61 -->

## Phase 12: Responsive Design & Mobile Testing
- [ ] Test stats widget on mobile (should be 1-column, full-width cards). <!-- task-62 -->
- [ ] Test charts on mobile (should be full-width, scrollable horizontally if needed). <!-- task-63 -->
- [ ] Test chart grid collapse (desktop 2-col → tablet 1-col). <!-- task-64 -->
- [ ] Test relation manager table on mobile (key columns visible, horizontal scroll for others). <!-- task-65 -->
- [ ] Verify no horizontal overflow on any device. <!-- task-66 -->

## Phase 13: Performance & Query Optimization
- [ ] Verify indexes on pr_reports(developer_id) and pr_reports(created_at) exist. <!-- task-67 -->
- [ ] Optimize queries: use select() to fetch only needed columns. <!-- task-68 -->
- [ ] Test performance with developer having 100+ reports. <!-- task-69 -->
- [ ] Consider caching stats if needed (optional: 1-hour TTL). <!-- task-70 -->
- [ ] Profile query count to ensure no N+1 problems. <!-- task-71 -->

## Phase 14: Integration & Navigation
- [ ] Add DeveloperResource to AdminPanelProvider registration. <!-- task-72 -->
- [ ] Verify navigation menu includes Developers link. <!-- task-73 -->
- [ ] Test navigation flow: Developers list → Developer detail → PR report detail (Task 2). <!-- task-74 -->
- [ ] Ensure back button/breadcrumbs work correctly. <!-- task-75 -->

## Phase 15: Testing & Validation
- [ ] Write feature tests for DeveloperResource pages (index, view). <!-- task-76 -->
- [ ] Test that charts render with sample data. <!-- task-77 -->
- [ ] Test that RelationManager displays all PR reports for a developer. <!-- task-78 -->
- [ ] Test clicking PR title navigates to correct detail page. <!-- task-79 -->
- [ ] Test filter functionality (Risk Level filter works). <!-- task-80 -->
- [ ] Test sorting (default by created_at, newest first). <!-- task-81 -->
- [ ] Test pagination (10 per page). <!-- task-82 -->
- [ ] Test empty states (new developer, no reports). <!-- task-83 -->

## Phase 16: Polish & Finalization
- [ ] Review all colors for WCAG AA contrast compliance. <!-- task-84 -->
- [ ] Verify all icons are Heroicons for consistency. <!-- task-85 -->
- [ ] Final RTL and localization audit. <!-- task-86 -->
- [ ] Code cleanup and documentation. <!-- task-87 -->
- [ ] Update this tasks.md with completion status. <!-- task-88 -->
