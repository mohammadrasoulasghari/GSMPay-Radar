# Tasks: Implement PR Analysis Webhook & Data Storage

## Phase 1: Database & Models
- [x] Create migration for `developers` table with unique username and optional name/avatar fields. <!-- task-1 -->
- [x] Create migration for `pr_reports` table with extracted metric columns and JSON payload column. <!-- task-2 -->
- [x] Create `Developer` Eloquent model with custom queries and mass-assignable fields. <!-- task-3 -->
- [x] Create `PrReport` Eloquent model with relationships and accessors for calculated fields. <!-- task-4 -->
- [x] Run migrations and verify table structures in database. <!-- task-5 -->

## Phase 2: Webhook Controller & Validation
- [x] Create `PrAnalysisWebhookController` with POST method. <!-- task-6 -->
- [x] Implement request validation rules (required fields: repository, pr_number, pr_link, title, author, ai_analysis). <!-- task-7 -->
- [x] Add custom validation to ensure ai_analysis is a valid JSON object. <!-- task-8 -->
- [x] Create `StorePrAnalysisRequest` FormRequest for reusable validation. <!-- task-9 -->
- [x] Register webhook route: `POST /api/webhooks/pr-analysis`. <!-- task-10 -->

## Phase 3: Business Logic
- [x] Implement developer upsert logic (check by username, create/update). <!-- task-11 -->
- [x] Implement tone_score calculation from reviewers_analytics array with null/empty handling. <!-- task-12 -->
- [x] Implement data extraction method (maps ai_analysis fields to pr_reports columns with defaults). <!-- task-13 -->
- [x] Create transaction wrapper for atomic developer + report creation. <!-- task-14 -->

## Phase 4: API Endpoint & Response
- [x] Implement webhook controller action (validate → developer sync → extract data → save report → respond). <!-- task-15 -->
- [x] Return 201 with report ID and developer ID on success. <!-- task-16 -->
- [x] Return 422 with validation errors on failure. <!-- task-17 -->
- [x] Add error logging for unexpected exceptions (return 500). <!-- task-18 -->

## Phase 5: Testing & Validation
- [x] Write unit tests for Developer upsert logic. <!-- task-19 -->
- [x] Write unit tests for tone_score calculation with edge cases (empty reviewers, null values). <!-- task-20 -->
- [x] Write feature tests for webhook endpoint (valid payload, missing fields, malformed JSON). <!-- task-21 -->
- [x] Test developer name update scenario. <!-- task-22 -->
- [x] Test duplicate PR analysis (ensure both records created). <!-- task-23 -->
- [x] Verify database indices are created and queries perform well. <!-- task-24 -->

## Phase 6: Documentation & Cleanup
- [ ] Document webhook endpoint and payload schema in API docs. <!-- task-25 -->
- [ ] Add inline code comments for complex logic (tone_score calc, data extraction). <!-- task-26 -->
- [ ] Create README snippet for n8n integration setup. <!-- task-27 -->
