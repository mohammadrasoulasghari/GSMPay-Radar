# Spec: Developer Resource Foundation

## ADDED Requirements

### Requirement: Developer Resource Class
The system SHALL provide a Filament resource class for managing developers that includes list, create, edit, and view pages.

#### Scenario: List All Developers
**Given** a user accesses the Developers resource from the admin menu  
**When** the page loads  
**Then** display a paginated table of all developers with columns:
- Username (sortable)
- Name (sortable)
- Total Reports (count of related pr_reports, read-only)
- Last Activity (most recent PR report created_at, read-only)
- Created At (sortable)

**And** the list view is searchable by username or name.

#### Scenario: View Developer Detail Page
**Given** a user clicks on a developer row  
**When** the view page loads  
**Then** display:
- Developer name as page title
- Developer avatar (if available) in header
- Username/GitHub handle as subtitle
- Stats overview widget (4 stat cards)
- Performance trends section (2 charts)
- PR reports relation manager below

**And** the page header includes breadcrumb: Developers > Developer Name

#### Scenario: Edit Developer (Optional)
**Given** developer metadata needs updating  
**When** user clicks Edit on a developer  
**Then** present form to edit:
- Name
- Avatar URL (text input with preview)

**And** webhook system auto-updates these from GitHub; manual edit is backup only.

#### Scenario: Create Developer (Manual)
**Given** need to manually add a developer before PR analysis arrives  
**When** user uses Create button  
**Then** present form with:
- Username (required, unique)
- Name (optional)
- Avatar URL (optional)

**And** system prevents duplicate usernames.

---

## MODIFIED Requirements

### Requirement: Developer Model Enhancement
The Developer model SHALL include performance calculation and aggregation methods for dashboard display.

#### Scenario: Calculate Average Metrics
**Given** a developer with 5+ pr_reports  
**When** requesting dashboard stats  
**Then** system calculates:
- Average tone_score from all reports
- Average solid_compliance_score from all reports
- Total count of reports
- Overall health status from recent 5 reports

**And** returns 0 or null if no reports exist (graceful fallback).

#### Scenario: Retrieve Trend Data for Charts
**Given** charting component needs historical data  
**When** calling getTrendData() method  
**Then** return array of objects: `[{date, solid_compliance, business_value, tone_score}, ...]`  
**And** array is sorted by created_at ascending (oldest first)  
**And** if <2 data points, return empty array to trigger fallback message.

#### Scenario: Detect Health Status from Recent Reports
**Given** dashboard needs to display overall health badge  
**When** calculating health status  
**Then** examine the last 5 reports:
- If >60% have health_status='healthy' → return 'healthy' (green)
- If >60% have health_status='warning' → return 'warning' (yellow)
- If any 'critical' → return 'critical' (red)
- If no reports → return 'unknown' (gray)

