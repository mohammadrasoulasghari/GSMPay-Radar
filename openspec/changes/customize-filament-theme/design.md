# Design: Filament Theme Customization

## Color Palette
We will move away from the default `Color::Amber`.
Proposed palette:
- **Primary**: A custom shade of Blue/Indigo to represent "Pay" and "Radar" (Trust & Tech).
- **Gray**: Neutral grays for a minimal background.

## Typography
The project currently uses `Vazirmatn`. We will keep this as it supports Persian/Arabic script which is likely needed given the user's language (Farsi). We might adjust weights if needed for a minimal look.

## Implementation Details
We will modify `app/Providers/Filament/AdminPanelProvider.php`.
We will use Filament's `Color` facade or define custom RGB values if a specific hex is chosen.

## Branding
We should consider adding a logo or changing the brand name in the panel configuration to "GSMPay Radar".
