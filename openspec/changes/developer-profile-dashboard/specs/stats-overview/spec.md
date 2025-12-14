# Spec: Stats Overview Widget

## ADDED Requirements

### Requirement: Stats Overview Display
The system SHALL display a 4-card stats widget at the top of the developer view page showing aggregate performance metrics.

#### Scenario: Display Total Reports Count
**Given** a developer with 12 analyzed PRs  
**When** opening the developer view  
**Then** Stats widget shows:
- Card title: "Total Reports"
- Card value: "12"
- Card icon: file or document icon (Heroicons)
- No color coding (neutral display)

#### Scenario: Display Average Tone Score with Color
**Given** a developer with tone scores: 85, 92, 78, 88  
**When** calculating stats  
**Then** Stats widget shows:
- Card title: "Avg. Tone Score"
- Card value: "85.75" (rounded to 2 decimals)
- Card color: "success" (green) because 85.75 >= 80
- Card icon: smiley-face or speech-bubble icon

**And** if tone scores < 50: color is "danger" (red)  
**And** if tone scores 50-80: color is "warning" (yellow)

#### Scenario: Display Compliance Rate with Color
**Given** a developer with solid_compliance_scores: 72, 68, 75, 71  
**When** calculating stats  
**Then** Stats widget shows:
- Card title: "Compliance Rate"
- Card value: "71.5%"
- Card color: "warning" (yellow) because 71.5 is in 50-80 range
- Card icon: code-bracket or check-circle icon

#### Scenario: Display Health Status Badge
**Given** a developer's recent 5 reports show mostly "healthy" status  
**When** calculating health status  
**Then** Stats widget shows:
- Card title: "Health Status"
- Card value badge: "Healthy" (green) or "Warning" (yellow) or "Critical" (red)
- Card icon: heart or shield icon
- Color reflects status

**And** if developer has no reports, show "Unknown" with gray color.

#### Scenario: Fallback for New Developer
**Given** a developer with zero PR reports  
**When** opening the view page  
**Then** each stat card shows:
- Title: as normal
- Value: "-" or "0"
- Color: "gray" (neutral)
- Tooltip (optional): "No data available yet"

**And** no errors or crashes occur.

#### Scenario: Handle Missing Tone Scores
**Given** a developer with some reports having null tone_score  
**When** calculating average tone score  
**Then** system ignores null values and averages only non-null scores.

**And** if all tone_scores are null, show "-" and gray color.

---

## MODIFIED Requirements

### Requirement: Header Widget Placement
The ViewDeveloper page SHALL use Filament's getHeaderWidgets() method to render stats widgets above the relation manager.

#### Scenario: Widgets Load Before Content
**Given** page is loading  
**When** widgets are rendered  
**Then** stats display in header area (above page content)  
**And** relation manager displays below.

