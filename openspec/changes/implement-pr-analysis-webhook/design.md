# Design: PR Analysis Webhook & Data Storage Architecture

## System Overview
```
n8n (AI Analysis Output)
    ↓ (POST JSON Payload)
Webhook Endpoint: POST /api/webhooks/pr-analysis
    ↓
Request Validation & Payload Parsing
    ↓
Developer Sync (Upsert by username)
    ↓
Data Extraction & Metric Calculation
    ↓
PrReport Record Creation
    ↓
Response (201 + Report ID)
```

## Database Schema Design

### developers Table
```sql
CREATE TABLE developers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NULLABLE,
    avatar_url VARCHAR(255) NULLABLE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```
**Key Points:**
- `username` is the unique identifier (natural key for deduplication)
- `name` can be updated if a new request has a different name
- `avatar_url` optional for future profile pictures

### pr_reports Table
```sql
CREATE TABLE pr_reports (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    developer_id BIGINT UNSIGNED NOT NULL,
    
    -- PR Identification
    repository VARCHAR(255) NOT NULL,
    pr_number VARCHAR(50) NOT NULL,
    pr_link VARCHAR(500) NULLABLE,
    title VARCHAR(500) NULLABLE,
    
    -- Extracted Metrics (Column-Based for Fast Queries)
    business_value_score INT NULLABLE DEFAULT 0,
    solid_compliance_score INT NULLABLE DEFAULT 0,
    tone_score DECIMAL(5, 2) NULLABLE DEFAULT 0,
    health_status VARCHAR(50) NULLABLE,
    risk_level VARCHAR(50) NULLABLE,
    change_type VARCHAR(100) NULLABLE,
    
    -- Raw Data (Complete JSON for detailed access)
    raw_analysis JSON NOT NULL,
    
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (developer_id) REFERENCES developers(id) ON DELETE CASCADE,
    INDEX idx_developer_id (developer_id),
    INDEX idx_repository_pr (repository, pr_number),
    INDEX idx_created_at (created_at)
);
```
**Key Points:**
- Foreign key to `developers` for referential integrity
- Multiple analyses per PR allowed (no unique constraint on pr_number)
- Extracted metrics in dedicated columns for filtering, aggregation, and dashboard queries
- Full JSON stored for audit trail and future metric extraction
- Indexes on common query paths

## Data Extraction Logic

### Tone Score Calculation
```php
// From ai_analysis.reviewers_analytics array
$reviewers = $ai_analysis['reviewers_analytics'] ?? [];
if (empty($reviewers)) {
    $tone_score = 0; // Default if no reviewers
} else {
    $tone_scores = array_map(fn($r) => $r['tone_score'] ?? 0, $reviewers);
    $tone_score = count($tone_scores) > 0 
        ? array_sum($tone_scores) / count($tone_scores) 
        : 0;
}
```

### Field Extraction with Defaults
```php
$extracted = [
    'business_value_score' => $ai_analysis['business_value_clarity'] ?? 0,
    'solid_compliance_score' => $ai_analysis['solid_compliance_score'] ?? 0,
    'tone_score' => $calculatedToneScore,
    'health_status' => $ai_analysis['health_status'] ?? 'unknown',
    'risk_level' => $ai_analysis['risk_level'] ?? 'unknown',
    'change_type' => $ai_analysis['change_type'] ?? 'unknown',
];
```

## API Contract

### Request Structure
```json
{
  "repository": "string",
  "pr_number": "string|int",
  "pr_link": "string (url)",
  "title": "string",
  "author": {
    "username": "string",
    "name": "string"
  },
  "ai_analysis": {
    "business_value_clarity": "int",
    "solid_compliance_score": "int",
    "health_status": "string",
    "risk_level": "string",
    "change_type": "string",
    "reviewers_analytics": [
      {
        "reviewer": "string",
        "tone_score": "int|float"
      }
    ]
  }
}
```

### Response (Success: 201)
```json
{
  "success": true,
  "message": "PR analysis report created",
  "data": {
    "report_id": 123,
    "developer_id": 45,
    "repository": "project-repo",
    "pr_number": "42"
  }
}
```

### Response (Validation Error: 422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "repository": ["The repository field is required."],
    "author.username": ["The author username field is required."]
  }
}
```

## Developer Sync Logic

1. **Check Existence**: Query `developers` table by `author.username`
2. **Update or Create**:
   - If exists: Update `name` if different, return ID
   - If not exists: Create new record, return ID
3. **Atomicity**: Use database transactions to ensure consistency

## Error Handling & Edge Cases

| Case | Handling |
|------|----------|
| Missing required fields | Return 422 validation error |
| AI analysis field is null/missing | Use sensible default (0 for scores, 'unknown' for enums) |
| Empty reviewers array (tone_score calc) | Set tone_score to 0 |
| Duplicate PR analysis (same PR, different time) | Create new record (historical tracking) |
| Developer name changes | Upsert by username, update name field |
| Malformed JSON in ai_analysis | Log error, reject request (422) |

## Performance Considerations
- Indexes on `developer_id`, `repository + pr_number`, and `created_at` for common queries
- JSON column allows flexible future expansion without schema migration
- Consider pagination/limiting for large report exports
