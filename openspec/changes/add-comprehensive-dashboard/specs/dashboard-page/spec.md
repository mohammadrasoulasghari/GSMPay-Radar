# Spec: Dashboard Page

## ADDED Requirements

#### Requirement: Dashboard Page Exists
The application must provide a Filament Dashboard page accessible at `/admin`.

**Scenarios:**
1. Unauthenticated user navigates to `/admin` → redirected to login
2. Authenticated admin navigates to `/admin` → Dashboard page loads with all widgets
3. Dashboard page renders with proper Filament layout (header, sidebar integration)
4. Page title is in Persian: "داشبورد تیم"

**Implementation Notes:**
- Extends `Filament\Pages\Dashboard`
- Implements `getWidgets(): array` returning dashboard widget classes
- Widgets registered in grid layout:
  - Row 1: Team Pulse (full width)
  - Row 2: Risk Radar (2/3 width) + Skill Chart (1/3 width)
  - Row 3: Leaderboard (full width)

---

#### Requirement: Dashboard Widget Management
The Dashboard page must allow registration and positioning of multiple widgets.

**Scenarios:**
1. Admin registers Team Pulse widget → appears in full-width row at top
2. Admin registers Risk Radar widget → appears in 2-column layout on second row
3. Admin registers Skill Gap widget → appears next to Risk Radar
4. Admin registers Leaderboard widget → appears full-width on third row
5. Widgets stack responsively on mobile (single column)
6. Dashboard loads all widgets without errors or 500 status

**Implementation Notes:**
- Uses Filament's grid-based layout system
- Each widget is a separate class with its own caching/queries
- Widgets are discoverable/auto-registered (if Filament supports)
- Refresh button on dashboard clears all widget caches

---

#### Requirement: Dashboard Load Performance
The Dashboard page must load within 2 seconds with optimized queries and caching.

**Scenarios:**
1. First dashboard load (no cache) → <5 seconds
2. Subsequent load (cache hit) → <1 second
3. User clicks refresh button → clears cache and reloads → <2 seconds
4. No N+1 queries on dashboard load

**Implementation Notes:**
- All widgets use `Cache::remember()` for aggregation results
- Cache duration: 1 hour (stats), 2 hours (skill chart)
- Manual refresh button invalidates all dashboard cache keys
- Database queries use appropriate eager loading (with relationships)
