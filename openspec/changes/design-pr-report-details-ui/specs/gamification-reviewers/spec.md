# Spec: Gamification & Reviewers Display

## ADDED Requirements

### Requirement: Badge Gallery Display
The gamification section MUST showcase earned badges with clear titles, visual icons, and Persian descriptions.

#### Scenario: Display Multiple Badges
Given raw_analysis.badges = [
  {name: "The Sniper", icon: "🎯", reason_fa: "دقت بالا در تحلیل"},
  {name: "Teacher", icon: "👨‍🏫", reason_fa: "کمک به یادگیری تیم"},
  {name: "Bug Hunter", icon: "🐛", reason_fa: "پیدا کردن باگ‌های مخفی"}
]
When rendered
Then displays as a gallery or card-based list
And each badge shows:
  - Badge name (e.g., "The Sniper")
  - Icon or color indicator
  - Persian description/reason (e.g., "دقت بالا در تحلیل")
And badges are visually distinct with colors (positive badges green/gold, negative red/orange)

#### Scenario: Positive vs Negative Badge Styling
Given a badge is positive (e.g., "Teacher", "The Sniper")
When rendered
Then uses green or gold background color
And conveys achievement

Given a badge is negative or warning type
When rendered
Then uses red or orange background
And conveys caution

#### Scenario: No Badges Earned
Given raw_analysis.badges = [] (empty array)
When rendered
Then displays a friendly message: "No badges yet. Keep improving!" (in Persian if needed)
And does not show empty list
And message is in muted/italic style

#### Scenario: Missing Badges Array
Given raw_analysis.badges is null or missing
When rendered
Then displays the same fallback message
And no error occurs

### Requirement: Reviewer Statistics Display
The gamification section MUST show detailed reviewer feedback with metrics and comment samples.

#### Scenario: Display Reviewer List
Given raw_analysis.reviewers_analytics = [
  {reviewer: "Alice", tone_score: 8.5, nitpicking_ratio: 0.15, comments: [...]},
  {reviewer: "Bob", tone_score: 7.2, nitpicking_ratio: 0.25, comments: [...]}
]
When rendered
Then displays as a table, list, or card-based layout
And each reviewer entry shows:
  - Reviewer name (e.g., "Alice")
  - Tone Score (e.g., "8.5/10") with color coding
  - Nitpicking Ratio (e.g., "15%") with interpretation

#### Scenario: Tone Score Color Logic
Given tone_score = 8.5 (high)
When rendered
Then displays in green (tone_score > 7)
And user perceives positive feedback

Given tone_score = 7.2 (moderate)
When rendered
Then displays in yellow (tone_score 5-7)

Given tone_score = 4.0 (low)
When rendered
Then displays in red (tone_score < 5)
And user perceives constructive but firm feedback

#### Scenario: Nitpicking Ratio Interpretation
Given nitpicking_ratio = 0.15 (15%)
When rendered
Then displays "15%" with label "Constructive feedback"
And green or neutral color

Given nitpicking_ratio = 0.35 (35%)
When rendered
Then displays "35%" with label "High nitpicking"
And yellow warning color

#### Scenario: Comment Samples in Collapsible Section
Given reviewer entry has comments array with best/worst examples
When rendered
Then displays "View Comments" or similar toggle/button
And when clicked, shows a collapsible section with:
  - Best comment sample (highlighted in green)
  - Worst comment sample (highlighted in red/orange)
And collapses to save screen space by default

#### Scenario: Limit Reviewer Display
Given raw_analysis.reviewers_analytics has 8 reviewers
When rendered
Then shows top 5 reviewers by default
And provides "View All Reviewers" button to expand
Or implements pagination for full list

#### Scenario: No Reviewer Feedback
Given raw_analysis.reviewers_analytics = [] (empty)
When rendered
Then displays: "No reviewer feedback available"
And does not show empty table/list

#### Scenario: Missing Reviewers Array
Given raw_analysis.reviewers_analytics is null or missing
When rendered
Then displays the fallback message
And no error occurs

#### Scenario: Incomplete Reviewer Data
Given a reviewer entry is missing tone_score or nitpicking_ratio
When rendered
Then shows "-" or "N/A" for missing values
And does not crash

### Requirement: Badges and Reviewers in Tabbed Interface
The gamification section MUST organize badges and reviewers in separate, switchable tabs.

#### Scenario: Tabs Organization
Given the gamification section is displayed
When rendered
Then shows two tabs:
  - Tab 1: "Badges" (or "نشان‌ها" in Persian)
  - Tab 2: "Reviewers" (or "بررسی‌کنندگان" in Persian)
And initially active tab shows badges gallery
And clicking Reviewers tab shows reviewer statistics
And switching between tabs is smooth

#### Scenario: Tab Styling
Given tabs are displayed
When rendered
Then active tab has distinct styling (underline, highlight, color change)
And inactive tab is visually de-emphasized
And tab styling matches GSMPay Radar theme

### Requirement: Gamification Section Title and Styling
The gamification section MUST maintain visual hierarchy and premium appearance.

#### Scenario: Section Title and Layout
Given the gamification section is displayed
When rendered
Then displays title: "Achievements & Feedback" (Persian: "دستاوردها و بازخورد")
And has clear visual separation from other sections
And background or border treatment distinguishes it
And color scheme matches overall theme (Indigo + Slate)
And sufficient padding creates visual breathing room
