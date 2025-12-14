# Project Context

## Purpose
GSMPay Radar analyzes pull requests through AI to provide comprehensive quality metrics for development teams. It tracks code health, team dynamics, and individual developer performance with real-time dashboard insights.

## Tech Stack
- **Backend**: Laravel 12.37.0 with Filament 4.2.0 admin panel
- **Database**: MySQL with PR analytics and developer tracking
- **Frontend**: Blade templates with Tailwind CSS, Filament components
- **CSS**: Tailwind CSS with custom Sky Blue theme (#0ea5e9)
- **Language**: 100% Persian (فارسی) UI, English API/internal code
- **Authentication**: Laravel Sanctum/session-based

## Project Conventions

### Code Style
- PHP: PSR-2 with Laravel conventions (4-space indentation)
- Blade: Component-based, type-hinted classes
- Database: Snake_case columns, camelCase model properties
- Translation keys: lowercase with underscores (e.g., `__('pr_report.health_status')`)

### Architecture Patterns
- **Models**: PrReport (analysis results), Developer (team members)
- **Filament**: Schema-based forms/infolists (not Form class)
- **Resources**: PrReportResource, DeveloperResource with custom Pages
- **Widgets**: Dashboard widgets for analytics visualization
- **Services**: Extractive methods on models (e.g., PrReport::extractMetrics)

### Testing Strategy
Feature tests for API endpoints, Unit tests for model methods, visual verification for dashboard widgets.

### Git Workflow
- Commits: Conventional format with detailed descriptions
- Branching: Feature branches from master
- OpenSpec: Proposal-driven development for major features

## Domain Context
**PR Analysis Metrics**:
- `health_status`: healthy|warning|critical - overall code quality
- `risk_level`: low|medium|high - production risk assessment
- `solid_compliance_score`: 0-100 - adherence to SOLID principles
- `business_value_score`: 0-100 - clarity of business value
- `tone_score`: 0-10 - reviewer sentiment analysis (mentorship quality)
- `raw_analysis`: JSON object from AI with full details (recurring_mistakes, reviewers_analytics, etc.)

**Team Dynamics**:
- Developers tracked individually with average metrics over time
- Tone score critical for detecting burnout/team morale
- Recurring mistakes identified for targeted training
- Reviewer quality measured separately from author metrics

**Dashboard Goals**:
- Executive view: quick health snapshot (Team Pulse)
- Risk management: identify critical PRs needing attention (Risk Radar)
- Skills assessment: find training gaps (Skill & Gap Chart)
- Engagement: reward and recognize reviewers (Leaderboard)

## Important Constraints
- No dark mode (light theme only, Sky Blue primary color)
- All UI text must be in Persian
- Performance: heavy queries must use caching (e.g., 1-hour cache on analytics)
- Live updates: widgets should support polling (30-second refresh recommended)
- Database: raw_analysis is stored as JSON, must handle parsing in queries/PHP

## External Dependencies
- Google Fonts (Vazirmatn font for Persian)
- Filament Admin Panel (widgets, layout system)
- Laravel Cache (for dashboard query optimization)
- AI Analysis JSON (from webhook integration, structured with metrics)

