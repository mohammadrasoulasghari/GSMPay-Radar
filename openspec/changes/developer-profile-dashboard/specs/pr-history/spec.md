# Spec: PR History RelationManager

## ADDED Requirements

### Requirement: PR Reports Relation Manager Table
The system SHALL display a sortable, filterable, paginated table of all PR reports analyzed for a developer.

#### Scenario: Display PR Report List
**Given** a developer with 15 analyzed PRs  
**When** opening the developer view  
**Then** PR Reports table displays:
- Column 1: **Title** (clickable link, primary column)
- Column 2: **Risk Level** (badge with color)
- Column 3: **Health Status** (icon + text)
- Column 4: **Created At** (formatted date, sortable)
- Pagination: showing 10 rows per page (2 pages total)

**And** each row represents one PR report.

#### Scenario: Click PR Title to View Details
**Given** user clicks on PR title in the table  
**When** link is activated  
**Then** navigate to PrReportResource::ViewPrReport page  
**And** display the detailed 4-section analytical dashboard (from Task 2).

#### Scenario: Risk Level Badge Color Coding
**Given** table displays risk_level column  
**When** rendering badges  
**Then** map risk_level to colors:
- "High" → Badge color: "danger" (red)
- "Medium" → Badge color: "warning" (yellow)
- "Low" → Badge color: "success" (green)
- null/missing → Badge color: "gray" (unknown)

**And** each badge displays the risk level text.

#### Scenario: Health Status Icon Display
**Given** table displays health_status column  
**When** rendering icons  
**Then** map health_status to icons + colors:
- "healthy" → Green checkmark icon + "Healthy" text
- "warning" → Yellow alert icon + "Warning" text
- "critical" → Red X icon + "Critical" text
- null/missing → Gray question mark + "Unknown" text.

#### Scenario: Created At Date Sorting
**Given** table is loaded  
**When** page renders  
**Then** default sort by Created At is descending (newest first)  
**And** user can click "Created At" column header to toggle ascending/descending.

**And** Created At displays in format: "Dec 14, 2025" or localized format.

#### Scenario: Sort by Other Columns
**Given** user clicks on column headers  
**When** clicking Title, Risk Level, or Health Status  
**Then** table sorts by that column  
**And** visual indicator (arrow) shows sort direction  
**And** sort preference is maintained during pagination.

---

## ADDED Requirements

### Requirement: PR Reports Filtering
The system SHALL provide a filter interface to scope the PR table by risk level.

#### Scenario: Filter by Risk Level
**Given** developer has PRs with mixed risk levels  
**When** clicking the filter button or filter UI  
**Then** display filter options:
- All (default, shows all PRs)
- High Risk
- Medium Risk
- Low Risk

**And** selecting a filter updates the table to show only matching PRs.

**And** pagination resets when filter changes.

#### Scenario: Clear Filters
**Given** filters are applied  
**When** user clicks "Clear Filters" or resets  
**Then** all filters are removed  
**And** table returns to showing all PRs.

#### Scenario: Filter Persistence (Optional)
**Given** user applies filter and navigates away/back  
**When** returning to the developer page  
**Then** filter may or may not persist (optional; URL query parameter ok).

---

## ADDED Requirements

### Requirement: Pagination & Table Responsive Design
The system SHALL implement pagination and responsive table layout for PR reports.

#### Scenario: Pagination with 10 Records Per Page
**Given** developer has 25 PRs  
**When** viewing PR reports table  
**Then** display:
- Page 1: PRs 1-10
- Page 2: PRs 11-20
- Page 3: PRs 21-25
- Pagination controls (prev/next, page numbers, "showing X of Y")

**And** can navigate between pages without reloading the entire page.

#### Scenario: Responsive Table on Mobile
**Given** table viewed on mobile (<768px)  
**When** page renders  
**Then** table adapts:
- Primary columns visible: Title, Risk Level
- Secondary columns (Health Status, Created At) hidden or accessible via horizontal scroll
- Each row is readable without excessive horizontal scrolling

**And** clicking row still links to PR detail page.

#### Scenario: Empty Table State
**Given** developer has no PR reports  
**When** opening the developer view  
**Then** relation manager shows:
- Blank state message: "No PR reports yet"
- Text: "Once this developer's PRs are analyzed, they will appear here"
- Optional: button to "View all reports" (navigates to PR Reports list with developer filter)

---

## MODIFIED Requirements

### Requirement: Read-Only Relation Manager
The PR Reports relation manager SHALL be read-only; no create, edit, or delete actions are available.

#### Scenario: No Action Buttons
**Given** user viewing PR reports table  
**When** looking at rows  
**Then** no "Edit", "Delete", or "Create" buttons appear in the relation manager  
**And** only "View" (click title) is the interaction.

