# Proposal: Add Comprehensive Team Dashboard (Global Team Radar)

## Summary
Create a comprehensive Filament dashboard page that provides leadership and team leads with a "helicopter view" of team health, risk exposure, and skill gaps. Four specialized widgets will surface critical metrics, identify risks, assess training needs, and celebrate reviewer excellence.

## Problem Statement
Currently, there is no central dashboard to quickly assess:
1. **Team Health**: Overall code quality and PR analysis trends
2. **Critical Risks**: Which PRs or developers pose production risk
3. **Skill Gaps**: Where the team needs targeted training
4. **Team Engagement**: Who is mentoring effectively (tone/reviewer quality)

This forces leadership to manually dig through individual PR reports, missing actionable insights.

## Proposed Solution
Build a multi-widget dashboard with:
1. **Team Pulse** (Stats Widget): Four key metrics in a header row
   - PRs Analyzed (Week)
   - Avg Code Health (solid_compliance_score)
   - Team Morale (Avg Tone Score)
   - Critical Risks Count

2. **Risk Radar** (Table Widget): Compact list of recent high-risk PRs
   - Filters: risk_level = high OR health_status = critical
   - Shows: developer, title, risk reason, timestamp

3. **Skill & Gap Chart** (Bar Chart Widget): Categorizes recurring mistakes
   - Counts most common error types from raw_analysis
   - Identifies training priorities (e.g., Testing, Naming Convention)

4. **Engagement Leaderboard** (List Widget): Recognizes excellent reviewers
   - Ranks by: tone_score average (mentorship quality)
   - Shows: reviewer name + badges earned

## Scope & Sequence
- **Phase 1**: Dashboard page scaffold + Team Pulse widget (queries, stats logic)
- **Phase 2**: Risk Radar table (query optimization, caching)
- **Phase 3**: Skill & Gap analysis (JSON parsing, aggregation)
- **Phase 4**: Leaderboard widget (reviewer metrics, badge display)

All phases include cache strategy, Persian translations, and live polling support.

## Success Criteria
✅ Dashboard loads in <2 seconds with caching  
✅ All four widgets functional and visually integrated  
✅ 100% Persian text for UI  
✅ Real-time updates via polling (30-second interval)  
✅ Widgets survive filter/sort interactions (if added later)  

## Open Questions / Clarifications
1. **Recurring Mistakes Format**: The `raw_analysis` JSON contains `recurring_mistakes` array. Should we parse these as free-text and count by keyword, or assume a structured category field?
   - *Assumption*: Free-text parsing with common keyword extraction (Testing, Naming, Documentation, Architecture, Performance, Security)

2. **Leaderboard Source**: Should we track reviewers as separate records, or extract from raw_analysis→reviewers_analytics array?
   - *Assumption*: Extract from raw_analysis for MVP, can refactor to separate Reviewer model later

3. **Cache Duration**: 1-hour cache on all aggregations?
   - *Assumption*: Yes, with manual refresh button for urgent updates

4. **Timezone**: Dashboard shows times in server timezone or user's local?
   - *Assumption*: Server timezone (UTC), can add user preference later

## Related Capabilities
- Depends on: PrReport model, Developer model (aggregation queries)
- May enable future: Custom filters, drill-down views, export features
