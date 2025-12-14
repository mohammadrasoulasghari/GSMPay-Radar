# Tasks: Design & Implement Chic PR Report Details UI

## Phase 1: Setup & Infrastructure
- [x] Create `PrReportResource` Filament resource class. <!-- task-1 -->
- [x] Define Infolist view method stub with 4-section structure. <!-- task-2 -->
- [x] Create color logic helper methods on PrReport model (getHealthColor, getBusinessValueColor, etc). <!-- task-3 -->
- [x] Create/register translation keys for Persian labels (resources/lang/fa/pr_report.php). <!-- task-4 -->
- [ ] Set up custom views directory for complex entry renders (resources/views/filament/entries/). <!-- task-5 -->

## Phase 2: Executive Header Section
- [x] Implement PR title and GitHub/GitLab link button. <!-- task-6 -->
- [x] Create Health Status card (with color badge and icon ♥️). <!-- task-7 -->
- [x] Create Risk Level card (with icon 🛡️ and severity color). <!-- task-8 -->
- [x] Create Business Value Score card (circular progress or badge). <!-- task-9 -->
- [x] Create Change Type card (tag-style display). <!-- task-10 -->
- [x] Ensure responsive 4-column → 2x2 → 1x4 grid on different screen sizes. <!-- task-11 -->

## Phase 3: Author Analysis Section (2-Column Grid)
- [x] Create left column: SOLID Compliance score card with color logic. <!-- task-12 -->
- [x] Create left column: Velocity assessment display. <!-- task-13 -->
- [x] Create left column: Test Coverage percentage with progress bar. <!-- task-14 -->
- [x] Create right column: Recurring Mistakes list with fallback text. <!-- task-15 -->
- [x] Create right column: Educational Path as clickable cards (title + reason_fa). <!-- task-16 -->
- [x] Ensure 2-column layout collapses to single column on mobile. <!-- task-17 -->

## Phase 4: Gamification & Reviewers Section
- [x] Create Badges subsection with badge gallery (name, icon/color, reason_fa). <!-- task-18 -->
- [x] Implement badge color logic (positive=green/gold, negative=red/orange). <!-- task-19 -->
- [x] Create "No badges yet" fallback message. <!-- task-20 -->
- [x] Create Reviewer Stats subsection as table or card list. <!-- task-21 -->
- [x] Show reviewer name, tone score (with color), and nitpicking ratio. <!-- task-22 -->
- [ ] Add collapsible section for best/worst comment samples per reviewer. <!-- task-23 -->
- [x] Create "No reviewer feedback" fallback message. <!-- task-24 -->
- [x] Wrap both subsections in Tabs component (Badges tab + Reviewers tab). <!-- task-25 -->

## Phase 5: Technical Debt Section
- [x] Create over-engineering alert box (yellow/warning style). <!-- task-26 -->
- [x] Implement conditional visibility: only show if raw_analysis.over_engineering === true. <!-- task-27 -->
- [x] Create Suggestions for Refactor list from raw_analysis.suggestions_for_refactor. <!-- task-28 -->
- [x] Add icon (wrench/code) to each refactoring item. <!-- task-29 -->
- [x] Create "No refactoring suggestions" fallback message. <!-- task-30 -->

## Phase 6: JSON Parsing & Computed Properties
- [x] Create accessor/computed methods for extracting color values from raw_analysis. <!-- task-31 -->
- [x] Create accessor for formatting reviewer data (tone score, nitpick ratio). <!-- task-32 -->
- [x] Create accessor for badges with fallback to empty array. <!-- task-33 -->
- [x] Create accessor for recurring mistakes with fallback. <!-- task-34 -->
- [x] Create accessor for educational path with fallback. <!-- task-35 -->
- [x] Create accessor for suggestions_for_refactor with fallback. <!-- task-36 -->

## Phase 7: Localization & RTL Support
- [x] Verify Filament respects RTL direction from AdminPanelProvider. <!-- task-37 -->
- [x] Test text alignment in all sections (should be natural RTL). <!-- task-38 -->
- [x] Add Persian labels for all badges and status enums. <!-- task-39 -->
- [ ] Test on both English and Persian locales. <!-- task-40 -->

## Phase 8: Responsive Design & Mobile Testing
- [x] Test Executive Header on mobile (cards should stack vertically). <!-- task-41 -->
- [x] Test Author Analysis grid collapses to single column on mobile. <!-- task-42 -->
- [x] Test Badges and Reviewers display properly on small screens. <!-- task-43 -->
- [ ] Verify no horizontal scroll on any screen size. <!-- task-44 -->
- [ ] Test Tabs component works well on touch devices. <!-- task-45 -->

## Phase 9: Null/Empty Handling & Edge Cases
- [x] Test section with missing/null badges array (should hide or show fallback). <!-- task-46 -->
- [x] Test section with missing reviewers_analytics (should show fallback). <!-- task-47 -->
- [x] Test missing educational_path (should show fallback). <!-- task-48 -->
- [x] Test missing suggestions_for_refactor (should show fallback). <!-- task-49 -->
- [ ] Test with incomplete raw_analysis (missing several keys). <!-- task-50 -->

## Phase 10: Testing & Documentation
- [ ] Write unit tests for color logic methods. <!-- task-51 -->
- [ ] Write feature tests for Infolist rendering (test all sections render). <!-- task-52 -->
- [x] Test that clicking GitHub/GitLab link opens correct URL. <!-- task-53 -->
- [ ] Test that educational path cards are clickable (if URL provided). <!-- task-54 -->
- [ ] Document section descriptions and expected JSON schema in code comments. <!-- task-55 -->
- [ ] Create README snippet showing example raw_analysis JSON structure. <!-- task-56 -->

## Phase 11: Performance Optimization
- [ ] Lazy-load images/avatars where applicable. <!-- task-57 -->
- [x] Limit reviewer list display (show top 5, provide "view all"). <!-- task-58 -->
- [ ] Implement pagination for long suggestions lists if needed. <!-- task-59 -->
- [ ] Use Collapse component for collapsible reviewer comments. <!-- task-60 -->

## Phase 12: Polish & Finalization
- [ ] Review all colors for contrast and readability (WCAG AA). <!-- task-61 -->
- [x] Verify all icons are consistent (Heroicons). <!-- task-62 -->
- [x] Final RTL and localization check. <!-- task-63 -->
- [x] Update tasks.md with completion status. <!-- task-64 -->
