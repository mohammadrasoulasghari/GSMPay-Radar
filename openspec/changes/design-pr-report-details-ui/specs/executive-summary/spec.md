# Spec: Executive Summary Display

## ADDED Requirements

### Requirement: PR Metadata Header
The report details view MUST prominently display PR identification information with direct links to source control.

#### Scenario: Display PR Title and Link
Given a PrReport record with valid title and pr_link
When the details view is rendered
Then the PR title is shown in large, readable text
And a clickable button labeled "View on GitHub" (or GitLab based on repository) opens the pr_link in a new tab
And the button uses an appropriate external link icon

#### Scenario: Missing PR Title
Given a PrReport with null or empty title
When the details view is rendered
Then a placeholder text "Untitled PR" or similar is shown
And no error is raised

### Requirement: Health Status Card
The view MUST display the current health status with visual indicators (color and icon).

#### Scenario: Display Health as Visual Badge
Given a PrReport with raw_analysis.health_status = "healthy"
When the Executive Header section is rendered
Then a card displays the health status
And the background color is green
And a heart icon (♥️) is shown
And accompanying text reads "Healthy"

#### Scenario: Warning Health Status
Given raw_analysis.health_status = "warning"
When rendered
Then the card displays yellow background
And accompanying icon indicates caution
And text reads "Warning"

#### Scenario: Critical Health Status
Given raw_analysis.health_status = "critical"
When rendered
Then the card displays red background
And icon indicates danger
And text reads "Critical"

#### Scenario: Unknown Health Status
Given raw_analysis.health_status is null or missing
When rendered
Then the card displays neutral gray background
And text reads "Unknown"
And no error occurs

### Requirement: Risk Level Card
The view MUST display risk level assessment with severity-based color coding and security-related iconography.

#### Scenario: Display Low Risk
Given raw_analysis.risk_level = "low"
When rendered
Then the Risk Level card shows green background
And a shield icon (🛡️) is displayed
And text reads "Low Risk"

#### Scenario: Display Medium Risk
Given raw_analysis.risk_level = "medium"
When rendered
Then the card shows yellow/amber background
And text reads "Medium Risk"

#### Scenario: Display High Risk
Given raw_analysis.risk_level = "high"
When rendered
Then the card shows red background
And text reads "High Risk"

### Requirement: Business Value Score Card
The view MUST display the business value clarity score with visual progress indication and color-coded thresholds.

#### Scenario: Score Below 50
Given raw_analysis.business_value_clarity = 35
When rendered
Then the card displays red background or progress color
And shows "35/100" or "35%" depending on implementation
And indicates low business value (red)

#### Scenario: Score Between 50 and 80
Given raw_analysis.business_value_clarity = 65
When rendered
Then the card displays yellow/warning background
And shows "65/100"
And indicates moderate business value (yellow)

#### Scenario: Score Above 80
Given raw_analysis.business_value_clarity = 92
When rendered
Then the card displays green background
And shows "92/100"
And indicates high business value (green)

#### Scenario: Missing Score
Given raw_analysis.business_value_clarity is null
When rendered
Then the card shows "N/A" or defaults to 0
And no error occurs

### Requirement: Change Type Display
The view MUST show the type of change (feature, bugfix, refactor, etc.) with appropriate styling.

#### Scenario: Display Feature Change
Given raw_analysis.change_type = "feature"
When rendered
Then the Change Type card displays "Feature"
And uses a consistent tag/badge style with neutral color (blue/indigo)
And optionally shows a related icon (e.g., sparkle or star)

#### Scenario: Display Bugfix Change
Given raw_analysis.change_type = "bugfix"
When rendered
Then displays "Bugfix" as a badge
And may use slightly different styling (e.g., subtle red tint)

#### Scenario: Unknown Change Type
Given raw_analysis.change_type is null or unknown value
When rendered
Then shows "Unknown" with neutral styling
And no error occurs

### Requirement: Responsive Header Layout
The Executive Header section MUST adapt its layout based on screen size without losing readability.

#### Scenario: Desktop Display (4-Column Grid)
Given viewport width ≥ 768px
When Executive Header renders
Then the 4 status cards (Health, Risk, Business Value, Change Type) display in a 2x2 grid or single 4-column row
And spacing and padding are generous
And text size is large enough for easy reading

#### Scenario: Tablet Display (2-Column Grid)
Given viewport width 640-768px
When Executive Header renders
Then cards rearrange to 2-column layout (2x2)
And padding adjusts appropriately

#### Scenario: Mobile Display (1-Column Stack)
Given viewport width < 640px
When Executive Header renders
Then all 4 cards stack vertically in a single column
And cards remain readable with preserved padding
And no horizontal scrolling is required

### Requirement: Executive Header Styling & Spacing
The Executive Header MUST use premium styling with clear visual separation and consistent spacing.

#### Scenario: Section Styling
Given the Executive Header is displayed
When rendered
Then the section has a distinct background or border
And title ("Executive Summary" in Persian: "خلاصه مدیریتی") is displayed prominently
And consistent padding/margins create visual breathing room
And color palette matches GSMPay Radar theme (Indigo + Slate)
