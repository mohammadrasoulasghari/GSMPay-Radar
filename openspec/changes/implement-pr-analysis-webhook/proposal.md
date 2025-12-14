# Proposal: Implement PR Analysis Webhook & Data Storage

## Summary
Build a webhook-based data ingestion system to receive PR analysis reports from n8n (with AI-generated insights), persist developer and PR report data into a structured database, and provide query endpoints for analytics and reporting.

## Motivation
GSMPay Radar needs to:
1. Store developer profiles in a canonical, non-duplicated manner
2. Capture detailed PR analysis metrics (scores, health status, risk levels) for historical tracking
3. Extract and normalize AI-generated data into database columns for fast querying and dashboard visualizations
4. Support multiple analyses per PR over time to track improvement

## Scope
- **Developer Entity**: Unique developers by username with idempotent upsert logic
- **PrReport Entity**: Timestamped PR analysis records with extracted metrics and full JSON payload
- **Webhook API**: POST `/api/webhooks/pr-analysis` endpoint to ingest n8n payloads
- **Data Extraction & Validation**: Parse AI analysis JSON, compute derived metrics (e.g., average tone_score), handle edge cases

## Risks & Mitigations
- **Schema Evolution**: JSON payloads from AI may change; we store full JSON and extract key fields to columns. If new fields emerge, we can add columns without losing data.
- **Duplicate Processing**: Webhook retries could cause duplicates; we accept this by design (track PR history), not deduplicating on PR number.
- **Data Quality**: AI may omit or null fields; we use sensible defaults (0 for numeric scores) and null safety in calculations.
- **Timezone Issues**: Store timestamps in UTC; client applications handle timezone display.

## Non-Goals
- Real-time notifications or webhooks triggered by report creation
- User authentication on webhook (assume n8n sends verified payloads; can add signature verification later)
- Dashboard/UI for reports (data layer only; UI is separate)
