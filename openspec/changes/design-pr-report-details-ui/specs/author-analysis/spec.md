# Spec: Author Analysis Display

## ADDED Requirements

### Requirement: SOLID Compliance Score Display
The Author Analysis section MUST show SOLID principle adherence with color-coded scoring.

#### Scenario: High SOLID Compliance
Given raw_analysis.solid_compliance_score = 88
When rendered
Then displays "88/100" in a card
And uses green background (score > 80)
And includes a brief label "SOLID Compliance"

#### Scenario: Moderate SOLID Compliance
Given raw_analysis.solid_compliance_score = 62
When rendered
Then displays "62/100" with yellow/warning background (50-80 range)
And includes the label

#### Scenario: Low SOLID Compliance
Given raw_analysis.solid_compliance_score = 35
When rendered
Then displays "35/100" with red background (< 50)
And includes the label

#### Scenario: Missing SOLID Score
Given raw_analysis.solid_compliance_score is null
When rendered
Then shows "N/A" or "-"
And no error occurs

### Requirement: Velocity Assessment Display
The Author Analysis section MUST show developer velocity or productivity metrics.

#### Scenario: Velocity Data Present
Given raw_analysis.velocity = "high" (or numeric value)
When rendered
Then displays velocity assessment
And uses appropriate styling/color
And helps evaluate author's productivity

#### Scenario: Missing Velocity Data
Given raw_analysis.velocity is null or missing
When rendered
Then section gracefully hides or shows placeholder
And does not cause errors

### Requirement: Test Coverage Display
The Author Analysis section MUST display the percentage of code covered by tests.

#### Scenario: Good Test Coverage
Given raw_analysis.test_coverage_percentage = 85
When rendered
Then displays "85%" with a progress bar or percentage display
And uses green color (coverage > 80)
And includes label "Test Coverage"

#### Scenario: Poor Test Coverage
Given raw_analysis.test_coverage_percentage = 42
When rendered
Then displays "42%" with yellow background (40-79%)
And warns of insufficient coverage

#### Scenario: Missing Coverage Data
Given raw_analysis.test_coverage_percentage is null
When rendered
Then shows "No data" without error

### Requirement: Recurring Mistakes List
The Author Analysis section MUST display mistakes the developer tends to repeat, formatted as actionable insights.

#### Scenario: Display List of Mistakes
Given raw_analysis.recurring_mistakes = ["Naming inconsistency", "Missing error handling", "Hardcoded values"]
When rendered
Then displays as an unordered list or bullet points
And each item is clearly readable
And includes an icon (e.g., 🎯 or ⚠️) next to each item

#### Scenario: No Recurring Mistakes Detected
Given raw_analysis.recurring_mistakes = [] (empty array)
When rendered
Then displays a positive message in italics: "No recurring mistakes detected - Excellent!"
And does not show an empty list

#### Scenario: Missing Recurring Mistakes Array
Given raw_analysis.recurring_mistakes is null or missing
When rendered
Then displays the same positive fallback message
And no error occurs

### Requirement: Educational Path Recommendations
The Author Analysis section MUST suggest learning resources and improvement areas with actionable links.

#### Scenario: Display Educational Cards
Given raw_analysis.educational_path = [
  {title: "Design Patterns", reason_fa: "برای بهبود معماری", url: "https://example.com/design-patterns"},
  {title: "TDD Basics", reason_fa: "برای بهتر شدن تست‌نویسی", url: "https://example.com/tdd"}
]
When rendered
Then displays as card-based list or clickable items
And each card shows:
  - Title (e.g., "Design Patterns")
  - Reason in Persian (e.g., "برای بهبود معماری")
  - Icon (e.g., 📚)
And if url is present, the card/title is clickable

#### Scenario: Recommendation Without URL
Given educational path entry has no url field
When rendered
Then displays the card as text-only (no click action)
And title and reason are still visible

#### Scenario: No Educational Recommendations
Given raw_analysis.educational_path = [] (empty array)
When rendered
Then displays: "No recommendations at this time - Keep up the good work!"
And does not show empty list

#### Scenario: Missing Educational Path
Given raw_analysis.educational_path is null or missing
When rendered
Then displays the fallback message
And no error occurs

### Requirement: Two-Column Layout for Author Analysis
The Author Analysis section MUST use a responsive two-column layout to balance information density.

#### Scenario: Desktop Layout (2 Columns)
Given viewport width ≥ 768px
When Author Analysis renders
Then the section displays in 2 columns:
  - Left column: SOLID, Velocity, Test Coverage cards
  - Right column: Recurring Mistakes list + Educational Path cards
And spacing between columns is clear

#### Scenario: Mobile Layout (1 Column)
Given viewport width < 640px
When Author Analysis renders
Then the section stacks all content in a single column
And left column content appears first
Then right column content follows
And no horizontal scrolling occurs

### Requirement: Author Analysis Section Styling
The Author Analysis section MUST maintain visual consistency with premium styling.

#### Scenario: Section Title and Structure
Given the Author Analysis section is displayed
When rendered
Then displays section title: "Author Deep Dive" (Persian: "تحلیل نویسنده")
And has clear visual separation from other sections
And padding/spacing creates breathing room
And matches overall theme (Indigo + Slate colors)
