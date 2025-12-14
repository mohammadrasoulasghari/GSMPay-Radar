# Spec: Technical Debt Display

## ADDED Requirements

### Requirement: Over-Engineering Alert Box
The technical debt section MUST prominently warn users when the PR shows signs of unnecessary complexity.

#### Scenario: Over-Engineering Detected
Given raw_analysis.over_engineering = true
When Technical Debt section is rendered
Then displays an alert box with:
  - Warning icon (⚠️)
  - Title: "Over-Engineering Detected"
  - Message: "This PR shows signs of over-engineering. Consider simplifying the implementation."
And uses alert-style styling (amber/yellow background, dark text)
And the alert is visually prominent but not aggressive

#### Scenario: No Over-Engineering
Given raw_analysis.over_engineering = false
When rendered
Then the alert box is completely hidden
And no "No issues" message is shown (positive case is silent)

#### Scenario: Missing Over-Engineering Value
Given raw_analysis.over_engineering is null or missing
When rendered
Then treats as false and hides the alert
And no error occurs

### Requirement: Refactoring Suggestions List
The technical debt section MUST present actionable refactoring recommendations with visual hierarchy.

#### Scenario: Display Refactoring Suggestions
Given raw_analysis.suggestions_for_refactor = [
  "Extract service layer for business logic",
  "Simplify conditional logic using strategy pattern",
  "Remove dead code in utils.js",
  "Consolidate duplicate validation functions"
]
When rendered
Then displays as an unordered list
And each item is a bullet point with:
  - Icon (wrench 🔧 or code icon)
  - Suggestion text (clear and actionable)
  - Proper indentation for readability

#### Scenario: Suggestion Styling
Given refactoring suggestions are displayed
When rendered
Then each item uses a readable font size
And icon color complements the overall theme
And items are easy to scan visually

#### Scenario: Many Suggestions (10+)
Given raw_analysis.suggestions_for_refactor has 15+ items
When rendered
Then either:
  - Show all items with good scrolling, OR
  - Show first 10 with a "View All" button/expandable section
  - With pagination if needed
And performance remains good

#### Scenario: No Refactoring Needed
Given raw_analysis.suggestions_for_refactor = [] (empty array)
When rendered
Then displays a positive message:
  "No refactoring suggestions at this time. Great job!" (or similar in Persian)
And uses encouraging tone/style (light green or positive color)
And does not show empty list

#### Scenario: Missing Suggestions Array
Given raw_analysis.suggestions_for_refactor is null or missing
When rendered
Then displays the same positive fallback message
And no error occurs

#### Scenario: Suggestion Contains Special Characters or Code
Given a suggestion contains code snippets or special characters
When rendered
Then displays correctly without escaping/breaking formatting
And code is appropriately highlighted (if wrapped in backticks or code blocks)

### Requirement: Technical Debt Section Organization
The technical debt section MUST clearly separate and organize information for easy comprehension.

#### Scenario: Section Structure
Given the Technical Debt section is displayed
When rendered
Then organizes content as:
  1. Alert box (if over_engineering is true)
  2. Section heading: "Refactoring Opportunities" (Persian: "فرصت‌های بهبود")
  3. List of suggestions
And has clear visual separation from other sections
And maintains visual hierarchy

#### Scenario: Empty Technical Debt Section
Given both over_engineering = false AND suggestions_for_refactor = []
When rendered
Then displays a brief positive message: "No technical debt identified"
Or hides the entire section gracefully
And does not show empty content

### Requirement: Technical Debt Styling & Colors
The technical debt section MUST use appropriate color psychology for problem areas.

#### Scenario: Alert Color and Style
Given over-engineering alert is displayed
When rendered
Then uses warning color (amber/yellow) for background
And dark text for contrast
And icon is visually distinct
And styling matches Filament/Tailwind design system

#### Scenario: Suggestions Styling
Given refactoring suggestions are displayed
When rendered
Then uses neutral or slate-colored text/background
And wrench icon is appropriately colored (gray or slate)
And overall appearance is professional and helpful (not scary)
And matches GSMPay Radar theme (Indigo + Slate palette)

### Requirement: Responsiveness
The Technical Debt section MUST display properly on all screen sizes.

#### Scenario: Desktop Display
Given viewport width ≥ 768px
When Technical Debt renders
Then alert box (if present) spans full width
And suggestions list is readable with proper line length
And icon and text are well-aligned

#### Scenario: Mobile Display
Given viewport width < 640px
When Technical Debt renders
Then alert box stack vertically
And list items wrap properly
And no horizontal scroll occurs
And icon alignment adjusts for smaller screens

#### Scenario: Long Suggestion Text
Given a suggestion is very long (100+ characters)
When rendered on mobile
Then text wraps properly
And layout does not break
And readability is maintained
