# Academic Conference PDF Design System

## Overview

This document describes the professional multi-page academic conference PDF layout system designed for the International Scientific Online Conference (ISOC) platform. The design incorporates national identity elements while maintaining a clean, academic, and prestigious appearance suitable for Scopus/Google Scholar style proceedings.

## Design Philosophy

### Core Principles
- **Clean & Modern**: Minimalistic design with ample white space
- **Academic Professionalism**: Suitable for scientific publications
- **National Identity**: Subtle incorporation of country-specific elements
- **High Readability**: Optimized typography for A4 PDF format
- **Consistent Branding**: Unified visual identity across all pages

## Color System

Each country has a defined color palette:
- **Primary Color**: Dominant flag color (used for headers, accents)
- **Secondary Color**: Secondary flag element (gradients, contrasts)
- **Accent Color**: Gold/complementary color (special highlights)

### Example Palettes

| Country | Primary | Secondary | Accent |
|---------|---------|-----------|--------|
| Uzbekistan | #1eb53a (Green) | #0099b5 (Turquoise) | #c9a227 (Gold) |
| United Kingdom | #c8102e (Red) | #012169 (Blue) | #c9a227 (Gold) |
| Germany | #000000 (Black) | #dd0000 (Red) | #ffcc00 (Gold) |
| Japan | #bc002d (Red) | #1a1a2e (Dark) | #c9a227 (Gold) |
| Kazakhstan | #00afca (Sky Blue) | #ffc61e (Yellow) | #006994 (Deep Blue) |

## Page Types

### 1. Cover Page

**Elements:**
- Top accent strip (gradient using primary → secondary colors)
- "International Scientific Online Conference" badge
- ISOC logo emblem (circular)
- Country name in large serif typography
- Conference title section with decorative borders
- Date and location
- Country image (decorative, with overlay)
- Footer with logos and national color strip

**Styling:**
- Country name: 48px, serif, uppercase, letter-spacing: 6px
- Title: 22px, serif, uppercase, centered
- Subtitle: 12px, sans-serif, uppercase, letter-spacing: 3px

### 2. Table of Contents

**Elements:**
- Header with conference branding
- "Contents" title with underline
- Numbered article entries with:
  - Article number
  - Article title (serif font)
  - Author name (sans-serif, lighter)
  - Page range (right-aligned)

### 3. Article Pages

**Header:**
- 4mm accent bar (gradient)
- Conference info line
- ISOC logo mini
- Left: Platform + Country name
- Right: Conference title (truncated)

**Content Area:**
- "Research Article" badge
- Article title (15pt, serif, centered)
- Title underline (gradient bar)
- Author block (name, affiliation, email)
- Abstract section (gray background, left border)
- Keywords line
- Main body text (justified, 11pt, serif)
- Section headings (sans-serif, primary color)

**Footer:**
- Separator line
- Left: Country + Year
- Center: Page number badge (primary color background)
- Right: Website
- 3mm accent bar (gradient)

**Watermark:**
- Country code (very light, rotated 35°)
- Opacity: ~2%

### 4. Left Accent Bar

- Positioned on left edge
- 2.5mm width
- Vertical gradient (primary → secondary → fade)
- Height: ~60% of page

## Typography

### Font Families
- **Serif**: DejaVu Serif, Times New Roman, Georgia
  - Used for: Titles, body text, article content
- **Sans-serif**: DejaVu Sans, Arial, Helvetica
  - Used for: Labels, headers, captions, navigation

### Font Sizes
| Element | Size |
|---------|------|
| Country name (cover) | 48px |
| Conference title | 22px |
| Article title (content) | 15pt |
| Section heading | 11pt |
| Body text | 10.5pt |
| Abstract text | 9pt |
| Keywords | 9pt |
| Header/Footer text | 7-8pt |

### Line Heights
- Body content: 1.65
- Abstract: 1.5
- References: 1.4

## National Identity Elements

### Subtle Integration
1. **Color accents**: Headers, bars, badges, links
2. **Background pattern**: Very light (2-3% opacity) geometric patterns
3. **Country image**: Decorative, with gradient overlay
4. **Watermark**: Country code as page watermark

### Pattern Types by Region
- **European**: Geometric diamonds, clean lines
- **Asian**: Traditional motifs (very simplified)
- **Middle Eastern**: Arabesque patterns (minimal)
- **American**: Modern geometric shapes

## File Structure

```
resources/views/pdf/
├── article-full.blade.php          # Full article template
├── article-professional.blade.php   # Enhanced professional template
├── article-cover.blade.php         # Cover page only
├── collection-cover.blade.php      # Collection/proceedings cover
├── conference-proceedings.blade.php # Complete proceedings template
├── table-of-contents.blade.php     # TOC page
└── certificate*.blade.php          # Certificate templates
```

## Usage

### Generate Single Article PDF
```php
$service = new ArticlePdfService();
$path = $service->generateFromText($article, $country);
```

### Generate Conference Proceedings
```php
$service = new ArticlePdfService();
$path = $service->generateProceedings($conference);
```

### Generate Article Collection
```php
$service = new ArticlePdfService();
$path = $service->generateCollection($conference);
```

## Print Specifications

- **Paper Size**: A4 (210mm × 297mm)
- **Orientation**: Portrait
- **Margins**: Variable (embedded in design)
- **Color Mode**: CMYK-safe colors
- **Resolution**: Print-ready (300 DPI equivalent)
- **Bleed**: None required (white margins)

## Accessibility

- High contrast text (dark on light)
- Semantic heading structure
- Readable font sizes (minimum 8pt)
- Clear visual hierarchy

## Browser/PDF Generator Compatibility

Tested with:
- DomPDF (Laravel package)
- TCPDF
- FPDI (for PDF merging)

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 2.0.0 | Feb 2026 | Added professional templates, 50+ country colors |
| 1.5.0 | Jan 2026 | Added proceedings generation |
| 1.0.0 | Jan 2026 | Initial design system |
