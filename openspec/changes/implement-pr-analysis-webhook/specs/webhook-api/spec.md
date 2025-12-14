# Spec: Webhook API & Request Handling

## ADDED Requirements

### Requirement: Webhook Endpoint Availability
The system MUST provide a publicly accessible HTTP endpoint for n8n to send PR analysis payloads.

#### Scenario: Webhook Endpoint Registration
Given the system is deployed
When a POST request is made to `/api/webhooks/pr-analysis`
Then the endpoint accepts the request
And processes it without authentication (or with pre-configured token if security upgrade needed)

### Requirement: Request Payload Validation
The system MUST validate that all required fields are present and properly formatted before processing.

#### Scenario: Valid Payload Submission
Given a webhook request contains all required fields: repository, pr_number, pr_link, title, author (with username, name), and ai_analysis
When the request is submitted
Then validation passes
And the request is processed

#### Scenario: Missing Repository Field
Given a webhook request omits the repository field
When the request is submitted
Then validation fails
And a 422 response is returned with error message: "The repository field is required."
And no PrReport is created

#### Scenario: Missing Author Username
Given a webhook request includes author but author.username is missing
When the request is submitted
Then validation fails
And a 422 response is returned with error message: "The author.username field is required."
And no records are created

#### Scenario: Invalid PR Number Format
Given a webhook request has pr_number as a non-string/non-numeric value (e.g., object)
When the request is submitted
Then validation fails
And a 422 response is returned
And no records are created

#### Scenario: Malformed AI Analysis JSON
Given a webhook request has ai_analysis that is not a valid JSON object
When the request is submitted
Then validation fails
And a 422 response is returned with error message about ai_analysis format
And no records are created

#### Scenario: Missing AI Analysis Object
Given a webhook request omits the ai_analysis field
When the request is submitted
Then validation fails
And a 422 response is returned: "The ai_analysis field is required."
And no records are created

### Requirement: Successful Report Creation Response
The system MUST return a 201 Created response with relevant identifiers when a report is successfully created.

#### Scenario: Successful Webhook Processing
Given a valid webhook request is submitted
When all validations pass and records are created
Then the endpoint returns HTTP status 201
And the response body contains:
  - success: true
  - message: "PR analysis report created"
  - data.report_id: the ID of the created PrReport
  - data.developer_id: the ID of the Developer (new or existing)
  - data.repository: echo of the repository name
  - data.pr_number: echo of the PR number

### Requirement: Error Response Handling
The system MUST return appropriate error responses with descriptive messages for different failure scenarios.

#### Scenario: Validation Error Response Format
Given a webhook request with missing fields
When the request is processed
Then the endpoint returns HTTP status 422
And the response body contains:
  - success: false
  - message: "Validation failed"
  - errors: { field_name: ["error message"] }

#### Scenario: Server Error Handling
Given an unexpected database error occurs during processing
When the webhook request is processed
Then the endpoint returns HTTP status 500
And the response body contains:
  - success: false
  - message: "An unexpected error occurred"
And the error is logged for debugging

### Requirement: Transactional Data Consistency
The system MUST ensure that developer and report records are created atomically or rolled back together.

#### Scenario: Atomic Developer and Report Creation
Given a webhook request is processed
When the Developer is successfully created (or retrieved)
And the report extraction succeeds
And the PrReport creation begins
And an unexpected database error occurs during PrReport insertion
Then the entire transaction is rolled back
And neither the Developer nor the incomplete PrReport is persisted
And a 500 error is returned

#### Scenario: Concurrent Webhook Requests Safety
Given two webhook requests arrive for different PRs by the same author
When both are processed concurrently
Then both are successfully created
And no database lock contention causes failures
And both reports are linked to the same developer (via unique constraint on username)

### Requirement: API Documentation & Contract
The system MUST clearly document the webhook endpoint contract for n8n integration.

#### Scenario: API Documentation Available
Given a developer needs to configure n8n
When they reference the API documentation
Then they find:
  - Endpoint URL: `/api/webhooks/pr-analysis`
  - HTTP Method: POST
  - Required headers: Content-Type: application/json
  - Request payload schema (exact JSON structure)
  - Response schema (success and error responses)
  - Example curl command or Postman collection
