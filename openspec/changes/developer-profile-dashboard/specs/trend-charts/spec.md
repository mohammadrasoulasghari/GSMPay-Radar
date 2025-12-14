# Spec: Performance Trend Charts

## ADDED Requirements

### Requirement: Technical Quality Trend Chart
The system SHALL display a line chart visualizing code quality metrics (solid_compliance_score, business_value_score) over time to identify quality trends.

#### Scenario: Render Chart with Historical Data
**Given** a developer with 10 PR reports spanning 3 months  
**When** opening the developer view  
**Then** Technical Quality Trend chart displays:
- X-axis: PR creation dates (formatted as "Dec 14", "Dec 15", etc. or sequential)
- Y-axis: Score values (0-100)
- Two lines: solid_compliance_score (primary line) and business_value_score (secondary line)
- Legend identifying both lines
- Data points sorted chronologically (oldest first)

**And** chart is responsive: full-width on mobile, half-width on desktop (2-column grid).

#### Scenario: Interpret Trend Direction
**Given** chart displays historical compliance and value scores  
**When** manager views the chart  
**Then** visual pattern clearly shows:
- Upward trend: developer improving code quality over time
- Flat trend: consistent performance
- Downward trend: declining code quality (needs support)

**And** if upward: line color is green  
**And** if downward: line color is orange/red  
**And** if flat: line color is blue.

#### Scenario: Handle Missing or Low Data
**Given** a developer with fewer than 2 data points  
**When** attempting to render chart  
**Then** chart displays fallback message:
- "Not enough data to display technical quality trend"
- "Add at least 2 PR analyses to see trends"

**And** no chart renders, no errors occur.

#### Scenario: Handle Null Scores
**Given** some reports have null solid_compliance_score or business_value_score  
**When** building chart data  
**Then** system filters out null points or treats them as 0  
**And** chart still renders with available data.

---

### Requirement: Behavioral Trend Chart (Tone Score)
The system SHALL display a line chart showing tone_score trajectory over time to detect behavioral patterns and potential burnout.

#### Scenario: Render Tone Trend Chart
**Given** a developer with 8+ PR reports with tone scores  
**When** opening the developer view  
**Then** Behavioral Trend chart displays:
- X-axis: PR creation dates
- Y-axis: tone_score values (0-100)
- Single line: average reviewer tone_score per PR
- Color-coded line: green if stable/upward, yellow if slight decline, red if steep decline
- Data sorted chronologically (oldest first)

#### Scenario: Detect Burnout Warning Pattern
**Given** tone_score shows consistent decline (e.g., 90 → 80 → 70 → 60)  
**When** rendering the chart  
**Then** system:
- Display line in red color
- Add visual warning indicator: "Potential burnout detected"
- Show tooltip on chart: "Tone score declining—check in with developer?"

**And** manager can use this insight for 1-on-1 conversation.

#### Scenario: Stable Tone Pattern
**Given** tone_scores fluctuate around average (no clear trend)  
**When** rendering chart  
**Then** line color is blue  
**And** no warning is shown.

#### Scenario: Handle Missing Tone Data
**Given** all reports have null or zero tone_score  
**When** attempting to render chart  
**Then** display fallback:
- "No behavioral feedback available yet"
- "Tone scores will appear once reviewers provide feedback"

**And** chart does not render.

---

## ADDED Requirements

### Requirement: Chart Grid Layout
The system SHALL arrange both trend charts in a responsive 2-column grid that collapses to 1 column on smaller screens.

#### Scenario: Desktop Layout (1200px+)
**Given** user viewing on desktop  
**When** page renders  
**Then** charts display side-by-side in 2 columns:
- Left: Technical Quality Trend (50% width)
- Right: Behavioral Trend (50% width)

**And** both charts have equal height.

#### Scenario: Tablet Layout (768px-1199px)
**Given** user viewing on tablet  
**When** page renders  
**Then** charts stack vertically in 1 column:
- First: Technical Quality Trend (full width)
- Second: Behavioral Trend (full width)

#### Scenario: Mobile Layout (<768px)
**Given** user viewing on mobile  
**When** page renders  
**Then** charts display full-width, stacked vertically  
**And** charts are scrollable horizontally if necessary  
**And** no layout shift or overflow occurs.

#### Scenario: Chart Titles and Section Header
**Given** page displays trend section  
**When** rendering  
**Then** section header shows: "Performance Trends" (Persian: "روند عملکرد")  
**And** each chart has its own title:
- Technical Quality Trend (Persian: "روند کیفیت فنی")
- Behavioral Trend (Persian: "روند رفتاری").

