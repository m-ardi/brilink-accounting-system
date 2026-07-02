---
name: Sistem Informasi Akuntansi Agen BRILink
colors:
  surface: '#f9f9fc'
  surface-dim: '#dadadd'
  surface-bright: '#f9f9fc'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f3f6'
  surface-container: '#eeedf0'
  surface-container-high: '#e8e8eb'
  surface-container-highest: '#e2e2e5'
  on-surface: '#1a1c1e'
  on-surface-variant: '#42474d'
  inverse-surface: '#2f3033'
  inverse-on-surface: '#f0f0f3'
  outline: '#72787e'
  outline-variant: '#c2c7ce'
  surface-tint: '#396283'
  primary: '#00273e'
  on-primary: '#ffffff'
  primary-container: '#0b3d5c'
  on-primary-container: '#7fa8cc'
  inverse-primary: '#a2cbf0'
  secondary: '#51606e'
  on-secondary: '#ffffff'
  secondary-container: '#d2e1f2'
  on-secondary-container: '#566472'
  tertiary: '#371f00'
  on-tertiary: '#ffffff'
  tertiary-container: '#543200'
  on-tertiary-container: '#cc9a5f'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#cce5ff'
  primary-fixed-dim: '#a2cbf0'
  on-primary-fixed: '#001e31'
  on-primary-fixed-variant: '#1e4a6a'
  secondary-fixed: '#d5e4f5'
  secondary-fixed-dim: '#b9c8d9'
  on-secondary-fixed: '#0e1d29'
  on-secondary-fixed-variant: '#3a4856'
  tertiary-fixed: '#ffddb9'
  tertiary-fixed-dim: '#f3bd7f'
  on-tertiary-fixed: '#2b1700'
  on-tertiary-fixed-variant: '#633f0b'
  background: '#f9f9fc'
  on-background: '#1a1c1e'
  surface-variant: '#e2e2e5'
typography:
  hero-figure:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
  headline-lg:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-md:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  subheading:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  body:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-semibold:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
  label:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
  label-caps:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  sidebar_width: 240px
  topbar_height: 56px
  container_max_width: 1440px
  gutter: 16px
  margin_sm: 8px
  margin_md: 16px
  margin_lg: 24px
---

## Brand & Style
The design system is engineered for the high-precision environment of financial accounting. The brand personality is authoritative, reliable, and utilitarian, prioritizing data density and clarity over decorative flair. 

The aesthetic is rooted in **Modern Professionalism**, characterized by a flat UI, rigid grid structures, and thin borders. It evokes the feeling of a digital ledger—organized, systematic, and secure. Visual noise is eliminated to reduce cognitive load during prolonged data entry and auditing tasks.

## Colors
The palette uses high-contrast, sober tones to reinforce trust. 
- **Primary Blue** (#0B3D5C) is used for core navigation and primary actions, signifying stability.
- **Ledger Accent** (#C8912E) is reserved for the "Signature Ledger Strip" and highlighting critical financial status indicators.
- **Surface & Card** colors establish a clear hierarchy between the application workspace and interactive containers.
- **Functional Colors** (Success/Danger) are calibrated for high legibility against both white and paper-grey backgrounds to signal transaction statuses instantly.

## Typography
Inter is utilized for its exceptional legibility in technical interfaces. 
- **Tabular Numerals:** For all instances of financial figures, balances, and report tables, `font-variant-numeric: tabular-nums` is mandatory. This ensures that columns of numbers align vertically for easier visual scanning and comparison.
- **Hierarchy:** Use `hero-figure` for account balances and total amounts. `label-caps` should be used for table headers and metadata descriptors.

## Layout & Spacing
This design system employs a **Fixed Shell Layout** to maximize available space for data-heavy tables and forms.
- **Sidebar:** A fixed 240px left-hand navigation allows for persistent access to accounting modules.
- **Top Bar:** A slim 56px utility bar handles breadcrumbs, global search, and profile settings.
- **Content Area:** A fluid area that respects a maximum width of 1440px on large displays to prevent line lengths from becoming unreadable.
- **Data Density:** Use a compact 8px base grid for internal component spacing to ensure high information density without clutter.

## Elevation & Depth
In this design system, depth is communicated through **Structural Lines** rather than shadows.
- **Borders:** All cards, inputs, and sections are defined by a 1px border (#DEDEDA). 
- **Z-Axis:** Instead of shadows, use tonal shifts (Paper-grey background vs White cards) to indicate layers.
- **Interaction:** Hover states on interactive elements should use a subtle background color shift (e.g., $surface to $border-color) rather than an increase in shadow depth.

## Shapes
The shape language is disciplined and geometric.
- **Border Radius:** A consistent 4px radius is applied to all cards, buttons, and input fields. This provides a professional "softened-edge" look while maintaining a serious, structured feel.
- **Ledger Strip:** A signature 3px vertical line of #C8912E is applied to the left-most edge of "Summary Cards" or "Active Financial Reports" to anchor the user's eye and signify high-importance data.

## Components
- **Buttons:** Primary buttons use #0B3D5C with white text. Secondary buttons use a 1px #DEDEDA border with Text Primary. Corner radius is strictly 4px.
- **Input Fields:** Use 1px #DEDEDA borders. Focused states use a 1px #0B3D5C border (no glow). Labels use the `label` typography tier positioned above the field.
- **Financial Tables:** Rows must have a hover state (#F7F7F5). Use `tabular-nums` for all cell data. Cell padding is tight (8px vertical) to maximize row visibility.
- **Cards:** White background, 1px border, 4px radius. Major financial summaries must include the **Ledger Strip** on the left border.
- **Status Chips:** Rectangular with 2px radius (not pill-shaped). Use low-saturation backgrounds of Success/Danger/Warning colors with high-saturation text for readability.
- **Data Grids:** For complex reporting, use a "Zebra Stripe" alternating background pattern (#FFFFFF and #F7F7F5) to assist horizontal tracking across many columns.