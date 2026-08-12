# Design System & Template Rules — SMKP Minerba Internal Audit

All future views, components, and pages built for this application MUST adhere strictly to the established design template and aesthetic guidelines.

## Layout & Aesthetics Guidelines

1. **Base Layout**:
   - Always extend `@extends('layouts.app')`.
   - Use Google Font **Plus Jakarta Sans**.
   - Use **Bootstrap 5.3** and **Bootstrap Icons (`bi-*`)**.

2. **Sidebar Navigation**:
   - **Left Sidebar**: 260px fixed width (`.sidebar-wrapper`), Dark Slate Gradient (`linear-gradient(180deg, #0f172a 0%, #1e293b 100%)`).
   - **Main Content**: Right-side content wrapper (`margin-left: 260px;`).
   - Grouped navigation links with icons, active state highlights (`.sidebar-nav-link.active`), and bottom user profile footer.

3. **Color Palette & Themes**:
   - **Primary Background**: Slate Light (`#f8fafc`).
   - **Sidebar Header**: Dark Slate Gradient (`linear-gradient(180deg, #0f172a 0%, #1e293b 100%)`).
   - **Accent / Primary Buttons**: Sky Blue Gradient (`linear-gradient(135deg, #0284c7, #0369a1)`).
   - **Admin Theme**: Red/Danger accents (`#ef4444`, `bg-danger`).
   - **Auditor Theme**: Cyan/Info accents (`#0284c7`, `bg-info`).

4. **Card & Container Styling**:
   - Use `.card-custom` class (`border-radius: 16px`, soft shadow `0 10px 25px -5px rgba(0,0,0,0.04)`).
   - Add hover lift effect `transform: translateY(-2px)` on interactive cards.
   - Use icon stat boxes (`.stat-icon-box`, `width: 54px`, `height: 54px`, `border-radius: 14px`).

5. **Typography & Badges**:
   - Headings must use `.fw-bold.text-slate-800` with descriptive Bootstrap icon.
   - Use rounded pill badges (`.badge-role`, `.rounded-pill`, `.px-3.py-2`) for status, role, and count indicators.
