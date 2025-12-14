# Spec: Theme Customization

## MODIFIED Requirements

### Requirement: Custom Theme Colors
The Admin Panel MUST use a custom color palette that reflects the "GSMPay Radar" branding.

#### Scenario: Primary Color
Given the Admin Panel is accessed
When the user views the dashboard
Then the primary color should be a custom "Radar" blue/indigo instead of Amber.

### Requirement: Project Branding
The Admin Panel MUST display the correct project name.

#### Scenario: Brand Name
Given the Admin Panel is accessed
When the user looks at the sidebar or top bar
Then the brand name should be "GSMPay Radar".
