# Spec: Developer Management

## ADDED Requirements

### Requirement: Developer Unique Identification
The system MUST maintain a canonical developer record identified uniquely by their GitHub username to prevent duplicates.

#### Scenario: New Developer First Analysis
Given a PR analysis arrives with a previously unseen author.username
When the webhook processes the request
Then a new Developer record is created with the username and name
And the developer_id is returned to link the PR report

#### Scenario: Returning Developer with Same Name
Given a PR analysis arrives with an author.username that already exists
And the author.name is identical to the stored name
When the webhook processes the request
Then no duplicate Developer is created
And the existing developer_id is used for the PR report

#### Scenario: Returning Developer with Updated Name
Given a PR analysis arrives with an author.username that already exists
And the author.name differs from the stored name
When the webhook processes the request
Then the Developer record's name field is updated
And the existing developer_id is used for the PR report
And the old name is lost (last-write-wins)

### Requirement: Developer Profile Data
The system MUST store essential developer metadata for identification and future avatar support.

#### Scenario: Store Developer Avatar URL
Given a PR analysis arrives with author metadata (username, name, optional avatar)
When the webhook processes the request
Then if an avatar_url is provided, it is stored in the Developer record
And if no avatar is provided, the field remains null

### Requirement: Idempotent Developer Creation
The system MUST handle concurrent webhook requests safely without creating duplicate developers.

#### Scenario: Rapid Concurrent Requests
Given two webhook requests arrive simultaneously with the same author.username
When both are processed concurrently
Then exactly one Developer record exists in the database
And both PR reports are linked to the same developer_id
