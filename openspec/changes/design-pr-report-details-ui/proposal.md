# Proposal: Design Chic PR Report Details UI (Filament Infolist)

## Summary
Build a visually appealing and data-rich details view page for `PrReport` resources within Filament. Instead of a traditional form view, implement an analytical dashboard that parses the stored JSON data and presents it through organized sections, color-coded metrics, interactive widgets, badges, and visual hierarchies. The design should feel premium yet informative, with Persian RTL support.

## Motivation
Currently, PR reports are stored with extracted metrics in columns and a complete JSON payload. Users need a unified, beautiful interface to:
- Quickly assess report health (executive summary at a glance)
- Understand author performance and learning opportunities
- View reviewer feedback and gamification badges
- Identify technical debt and refactoring needs

This view transforms raw data into actionable insights through strategic layout, color psychology, and visual storytelling.

## Scope
- **4-Section Layout** using Filament Infolist (Executive Header, Author Deep Dive, Gamification, Technical Debt)
- **Executive Header** with PR metadata, health/risk/score cards, and action buttons
- **Author Analysis** (2-column grid) showing SOLID scores, test coverage, recurring mistakes, and educational paths
- **Gamification Section** with badge gallery and reviewer statistics
- **Technical Debt Section** with refactoring suggestions and over-engineering alerts
- **Color Coding Logic** for all numeric values and statuses
- **JSON Parsing** directly from `raw_analysis` field using Filament accessors/computed properties
- **Responsive Design** with mobile-first grid collapsing
- **Null/Empty Handling** with graceful fallbacks and conditional visibility

## Key Design Principles
1. **Visual Hierarchy**: Most important info (Health, Risk, Value) dominates the top
2. **Color Psychology**: Red=Critical, Yellow=Warning, Green=Healthy, Gold=Achievement
3. **Information Density**: Use sections, tabs, and collapsibles to avoid cognitive overload
4. **Actionable**: Links to repos, reviewer profiles, and learning resources
5. **Responsive**: Graceful adaptation from desktop (multi-column) to mobile (stacked)
6. **RTL-Friendly**: Proper text direction and alignment for Persian users

## Risks & Mitigations
- **JSON Structure Variance**: AI output format may differ; use safe accessors and fallbacks
  - Mitigation: Store complete JSON; handle missing/null keys with defaults
- **Performance on Large Data**: Complex JSON with many nested arrays
  - Mitigation: Limit reviewer list, use pagination/collapsibles for long lists
- **Localization**: Persian text in badges, labels
  - Mitigation: Create translation keys for all UI strings; store `reason_fa` in JSON
- **Mobile Rendering**: Too many cards/widgets on small screens
  - Mitigation: Use Filament's responsive grid (col-span-1 on mobile, col-span-2+ on desktop)

## Non-Goals
- Real-time updates or WebSocket subscriptions
- Editing/modifying report data (view-only)
- Export functionality (separate feature)
- Custom charting library (use Filament's built-in components)
