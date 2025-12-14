# Spec: PR Report Storage & Data Extraction

## ADDED Requirements

### Requirement: Store PR Metadata
The system MUST capture and persist the core PR identification fields for tracking and linking.

#### Scenario: Record PR Identification
Given a webhook request with repository, pr_number, pr_link, and title
When the request is processed
Then a PrReport record is created with these fields
And they are retrievable for report generation and filtering

### Requirement: Extract and Store Numeric Metrics
The system MUST extract numeric scoring metrics from the AI analysis payload and store them as dedicated columns for fast querying and aggregation.

#### Scenario: Store Business Value Score
Given ai_analysis contains a business_value_clarity field with integer value 85
When the webhook processes the payload
Then the business_value_score column is set to 85
And it is queryable and filterable

#### Scenario: Store SOLID Compliance Score
Given ai_analysis contains a solid_compliance_score field with integer value 72
When the webhook processes the payload
Then the solid_compliance_score column is set to 72

#### Scenario: Missing Business Value Score
Given ai_analysis does not contain business_value_clarity or it is null
When the webhook processes the payload
Then the business_value_score column defaults to 0
And no error is raised

#### Scenario: Calculate Average Tone Score from Reviewers
Given ai_analysis.reviewers_analytics is an array with 3 reviewers each having tone_score [8, 7, 9]
When the webhook processes the payload
Then the tone_score column is calculated as (8 + 7 + 9) / 3 = 8.0
And stored as a decimal value

#### Scenario: Empty Reviewers Array for Tone Score
Given ai_analysis.reviewers_analytics is an empty array
When the webhook processes the payload
Then the tone_score column defaults to 0
And no error is raised

#### Scenario: Null Tone Scores in Reviewers
Given ai_analysis.reviewers_analytics contains reviewers with some null tone_score values
When the webhook processes the payload
Then null values are treated as 0 in the average calculation

### Requirement: Extract and Store Status Enums
The system MUST extract status and classification fields and store them as strings for categorization and dashboards.

#### Scenario: Store Health Status
Given ai_analysis contains health_status with value "healthy"
When the webhook processes the payload
Then the health_status column is set to "healthy"

#### Scenario: Store Risk Level
Given ai_analysis contains risk_level with value "high"
When the webhook processes the payload
Then the risk_level column is set to "high"

#### Scenario: Store Change Type
Given ai_analysis contains change_type with value "feature"
When the webhook processes the payload
Then the change_type column is set to "feature"

#### Scenario: Missing Status Fields Default
Given ai_analysis does not contain health_status or it is null
When the webhook processes the payload
Then the health_status column defaults to "unknown"
And the report is still created

### Requirement: Store Raw JSON Payload
The system MUST persist the complete AI analysis JSON for audit, debugging, and future metric extraction without schema migration.

#### Scenario: Preserve Full AI Analysis
Given a webhook request with a complex ai_analysis object containing nested data
When the webhook processes the request
Then the entire ai_analysis object is stored as JSON in the raw_analysis column
And all nested fields and arrays are preserved exactly as received
And the raw data can be queried using JSON path expressions

### Requirement: Support Multiple Reports Per PR
The system MUST allow tracking multiple analyses for the same PR over time without deduplication.

#### Scenario: Same PR Analyzed Twice
Given a webhook request for repository "repo1" and pr_number "42" is received
When the report is created
And later a second webhook request arrives for the same repository and pr_number
Then a second PrReport record is created
And both records are linked to the same developer_id
And they can be retrieved with created_at timestamps for comparison

### Requirement: Link PR Report to Developer
The system MUST establish a referential relationship between PR reports and developers.

#### Scenario: Developer Association
Given a PrReport has been created with developer_id 5
When querying the report
Then the developer record is accessible via the relationship
And deleting the developer cascades delete to orphan reports
