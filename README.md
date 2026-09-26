# HoverSync Pro — *The Marionettist* 🎭
### Next-Gen Cross-Element Interaction, Hover & Scroll Animation Engine for WordPress & Elementor

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg?style=for-the-badge&logo=wordpress)](https://wordpress.org)
[![Elementor](https://img.shields.io/badge/Elementor-3.x%2B-92003B.svg?style=for-the-badge&logo=elementor)](https://elementor.com)
[![Engine](https://img.shields.io/badge/Engine-v1.2.0_Zero--Lag-7c3aed.svg?style=for-the-badge)](https://github.com)
[![Performance](https://img.shields.io/badge/FPS-60%20%2F%20120%20RAF%20Batching-10b981.svg?style=for-the-badge)](https://github.com)
[![License](https://img.shields.io/badge/License-GPL--2.0-orange.svg?style=for-the-badge)](LICENSE)

> **Codename: The Marionettist** 🎭  
> Just as a master puppeteer pulls invisible strings to bring multiple distant marionettes to life in perfect synchrony, **HoverSync Pro** connects any trigger element on your page with any number of receiver target elements across the entire DOM tree — without parent-child constraints or coding friction.
>
> This plugin isn't for beginners; you must have basic knowledge of Elementor

---
<div align="center">
<img align="center" src="https://lmwebdesigners.com/wp-content/uploads/2026/09/hoversync.png" width="50%"/>
  
</div>


---

## 📑 Table of Contents
- [✨ Core Philosophy & "The Marionettist" Architecture](#-core-philosophy--the-marionettist-architecture)
- [⚡ Key Features & Highlights](#-key-features--highlights)
- [🎭 The 3 Interaction Roles](#-the-3-interaction-roles)
- [🖱️ The 3 Trigger Event Types](#️-the-3-trigger-event-types)
  - [1. On Hover / Mouseover](#1-on-hover--mouseover)
  - [2. On Click / Toggle State](#2-on-click--toggle-state)
  - [3. On Scroll / In-Viewport Sync & Reverse Modes](#3-on-scroll--in-viewport-sync--reverse-modes)
- [🏎️ Performance & Zero-Lag Engine Specifications](#️-performance--zero-lag-engine-specifications)
- [🎨 Built-In Effect Catalog](#-built-in-effect-catalog)
- [🥞 Effect Stacking & Stagger Sequences](#-effect-stacking--stagger-sequences)
- [📱 Responsive Breakpoint Control](#-responsive-breakpoint-control)
- [🛠️ Step-by-Step Setup Workflow in Elementor](#️-step-by-step-setup-workflow-in-elementor)
- [💡 Real-World Use Cases & Code Recipes](#-real-world-use-cases--code-recipes)
- [📂 Plugin File Structure](#-plugin-file-structure)
- [🧩 Developer API & Hooks](#-developer-api--hooks)
- [🏷️ Button & UI Typography Standards](#️-button--ui-typography-standards)
- [📋 Changelog](#-changelog)
- [📜 License](#-license)

---

## ✨ Core Philosophy & "The Marionettist" Architecture

In traditional web design and Elementor page builders, hover and motion effects are locked to the specific element being interacted with. If you hover over a button, only that button changes state.

**HoverSync Pro ("The Marionettist") breaks this paradigm.** It introduces a decoupled, pub-sub style motion runtime designed specifically for Elementor:

```
┌─────────────────────────────────────────────────────────────┐
│                 PRIMARY TRIGGER (PUPPETEER)                 │
│  [ Button / Card / Icon / Scroll Section ] (Source)         │
└──────────────────────────────┬──────────────────────────────┘
                               │
               (Invisible Interaction Strings)
         ┌─────────────────────┼─────────────────────┐
         ▼                     ▼                     ▼
┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐
│ TARGET 1 (MARION.)│  │ TARGET 2 (MARION.)│  │ TARGET 3 (MARION.)│
│ #hero-3d-visual  │  │ #price-badge     │  │ .sibling-cards   │
│  • Scale 1.15x   │  │  • Color Glow    │  │  • Dim Opacity   │
│  • 3D Tilt Sync  │  │  • Rotate -6deg  │  │  • Delay 100ms   │
└──────────────────┘  └──────────────────┘  └──────────────────┘
```

### Core Architecture Components:
1. **Frontend Engine** (`assets/js/frontend.js`): Pure vanilla JS engine (under 12KB gzip). Zero dependencies, no heavy bloat, built-in GSAP-like cubic-bezier easing solvers, `requestAnimationFrame` render batcher, and `IntersectionObserver` viewport listener.
2. **Renderer** (`includes/class-renderer.php`): Compiles backend meta JSON into minimal, sanitized DOM `data-hoversync` payloads and injects scoped CSS variables.
3. **Controls Manager** (`includes/class-controls.php`): Injects native Elementor Advanced panel tabs with repeater layers, high-contrast inputs, and live preview hooks.
4. **Database Meta Manager** (`includes/class-database.php`): Stores effects structure as versioned post-meta schemas.
5. **AJAX & Editor Handler** (`includes/class-ajax-handler.php`): Handles dynamic nonce verification and instant editor canvas sync.

---

## ⚡ Key Features & Highlights

- **Cross-Section & Cross-DOM Sync**: Animate a footer element from a header trigger or vice-versa.
- **Simultaneous Multi-Target Stacking**: One trigger can manipulate multiple distant targets with distinct animation layers, durations, and delays.
- **Zero Layout Thrashing**: Animates only composited properties (`transform`, `opacity`, `filter`, `background-color`, `box-shadow`) without recalculating DOM geometry (`width`/`height`/`top`).
- **Smart GPU VRAM Management**: Automatically enables `will-change: transform` only during active interaction transitions and cleans it up immediately after.
- **GSAP-Quality Easings Out-of-the-Box**: Includes `Power4 Ultra Smooth`, `Back Overshoot`, `Elastic Pop`, `Power2`, and `Material Design` without loading external 60KB GSAP libraries.
- **IntersectionObserver Scroll Sync**: Smooth scroll triggers that do not attach continuous scroll event listeners.
- **Full Touch & Responsive Granularity**: Selectively activate complex sequences on `Desktop Only (>1024px)`, `Mobile Only (<=1024px)`, or `All Devices`.

---

## 🎭 The 3 Interaction Roles

Every Elementor widget equipped with HoverSync Pro can assume one of three interaction roles:

| Role | Alias | Description | Example |
| :--- | :--- | :--- | :--- |
| **Primary Trigger** | `Source` | Initiates animations when hovered, clicked, or scrolled into view. Holds the effect configuration rules. | CTA Button, Pricing Card, Section Wrapper |
| **Passive Receiver** | `Target` | Responds to signals emitted by Sources. Identified by a unique **Custom Element Alias** (e.g. `product-preview`). | Product Image, Floating Badge, Background Blob |
| **Hybrid** | `Both` | Acts as a Source to trigger other elements while simultaneously reacting to incoming trigger signals from external Sources. | Interactive Grid Tile, Connected Nav Item |

---

## 🖱️ The 3 Trigger Event Types

### 1. On Hover / Mouseover
- **Activation**: Triggers instantly on `mouseenter` / `pointerenter`.
- **Reversal**: Gracefully interpolates back to its rest state on `mouseleave`.
- **Usage**: Interactive e-commerce cards, navigation mega-menus, team member bios, portfolio showcases.

### 2. On Click / Toggle State
- **Activation**: Toggles target state ON or OFF on mouse click or touch tap.
- **Features**: Supports 3D flip states, expanded accordion panels, active filter matrices, and persistent modal states.
- **Usage**: Card flippers, feature tabs, interactive FAQs, floating action menus.

### 3. On Scroll / In-Viewport Sync & Reverse Modes
Powered by `IntersectionObserver` with customizable **Viewport Trigger Points (10% to 100%)**.

```
Viewport Top ─────────────────────── 0%
                                     │
      [ Trigger Line (e.g. 75%) ] ───┼──► Widget crosses 75% height -> ANIMATE!
                                     │
Viewport Bottom ──────────────────── 100%
```

#### The 3 Scroll Reverse Modes:
1. **`Reverse Only on Scroll Back Up (Recommended)`**:  
   - Scrolling down past the element activates and **locks** the animation in place.
   - Scrolling further down maintains the animation state.
   - Only scrolling back upward past the entry point reverses it.
   - *Best for:* Hero reveals, headline staggered slides, entrance feature sections.
2. **`Reverse on Any Viewport Exit`**:  
   - Activates whenever the element enters the viewport threshold.
   - Reverses whenever the element leaves the viewport in *either* direction (top or bottom).
   - *Best for:* Ambient looping highlights, floating viewport indicators.
3. **`Never Reverse (Run Once)`**:  
   - Activates once upon first entering the threshold and stays permanently locked.
   - *Best for:* Animated milestone counters, one-time progress bars, permanent entrances.

---

## 🏎️ Performance & Zero-Lag Engine Specifications

| Metric / Parameter | HoverSync Pro Specification |
| :--- | :--- |
| **Rendering Loop** | Native `window.requestAnimationFrame()` DOM write batching |
| **Frame Budget** | Rock-solid 60 FPS on mobile, 120 FPS on high-refresh Pro displays |
| **Layout Thrashing** | 0% (Strictly forbidden: `width`, `height`, `top`, `left`, `margin`, `padding`) |
| **GPU Memory (VRAM)** | Dynamic `will-change` allocation with auto-garbage collection |
| **Scroll Engine** | Native `IntersectionObserver` API (No passive scroll listener lag) |
| **Bundle Size** | ~11.8 KB minified vanilla JS |
| **Dependencies** | 0 (Pure Vanilla JS — jQuery & external GSAP scripts not required) |

### Built-in GSAP-Like Easing Curves

```javascript
// HoverSync Pro Easing Matrix
const EasingCurves = {
  power4:    'cubic-bezier(0.25, 1, 0.5, 1)',      // Ultra Smooth (Default)
  backOut:   'cubic-bezier(0.34, 1.56, 0.64, 1)',  // Back Overshoot (Snappy)
  elastic:   'cubic-bezier(0.68, -0.6, 0.32, 1.6)',// Spring Pop (Playful)
  power2:    'cubic-bezier(0.45, 0, 0.55, 1)',     // Medium Smooth
  material:  'cubic-bezier(0.4, 0, 0.2, 1)',       // Google Material Standard
  linear:    'linear'                              // Constant Speed
};
```

---

## 🎨 Built-In Effect Catalog

### 1. Hardware-Accelerated Transformations (GPU)
- **Scale (Zoom)**: Scale in/out from 0.0x to 3.0x (e.g. `1.15`).
- **Rotate (Spin & Tilt)**: Clockwise/counter-clockwise rotation in degrees (e.g. `-8deg` or `180deg`).
- **Translate X / Y (Move)**: Smooth horizontal & vertical offsets (e.g. `translateY(-15px)`).
- **Skew X / Y**: Dynamic isometric tilting (e.g. `skewX(4deg)`).

### 2. Visual Filters & Optical Effects
- **Glass Blur**: Frosted glass depth effect (e.g. `blur(12px)`).
- **Opacity (Fade)**: Fade in/out transitions (e.g. `0.35` for sibling dimming).
- **Grayscale**: Monochrome to full color reveal (`grayscale(100%)` to `0%`).
- **Brightness & Saturation**: Highlights and color pops (`brightness(1.3)`).

### 3. Styles, Colors & Shadows
- **Background Color**: Solid HEX, RGB, or semi-transparent RGBA transitions.
- **Text Color**: High-contrast typography color morphing.
- **Box Shadow / Neon Glow**: Soft atmospheric depth or vivid neon glows.
- **Border Color & Border Radius**: Dynamic corner morphing (e.g. `8px` to `32px`).

### 4. Custom CSS Declarations (Unlimited Flexibility)
Enter any valid CSS declaration string to animate advanced properties:
```css
clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%); backdrop-filter: blur(20px);
```

---

## 🥞 Effect Stacking & Stagger Sequences

With HoverSync Pro, a single trigger can execute multiple staggered effect layers simultaneously on the same target or across multiple different targets:

```json
{
  "id": "master-card-trigger",
  "role": "source",
  "event": "hover",
  "fx": [
    { "selector": "#target-badge", "type": "scale", "val": 1.2, "dur": 300, "ease": "back.out" },
    { "selector": "#target-image", "type": "scale", "val": 1.15, "dur": 450, "ease": "power4.out" },
    { "selector": "#target-image", "type": "rotate", "val": "-4deg", "dur": 450, "ease": "power2.out", "delay": 50 },
    { "selector": "#target-price", "type": "color", "val": "#a855f7", "dur": 300 },
    { "selector": "#target-button", "type": "translateY", "val": "0px", "dur": 400, "delay": 100 }
  ]
}
```

---

## 📱 Responsive Breakpoint Control

Ensure touch devices deliver an optimal user experience:
- **`All Devices`**: Operates on desktops, laptops, tablets, and phones.
- **`Desktop Only (>1024px)`**: Disables animations on touch screens to prevent sticky hover states.
- **`Mobile Only (<=1024px)`**: Specifically tuned for mobile-specific scroll and tap sequences.

---

## 🛠️ Step-by-Step Setup Workflow in Elementor

```
STEP 1: RECEIVER (TARGET)         STEP 2: TRIGGER (SOURCE)          STEP 3: PREVIEW & ENJOY
┌─────────────────────────┐       ┌─────────────────────────┐       ┌─────────────────────────┐
│ 1. Select Widget        │       │ 1. Select Trigger Widget│       │ 1. Move Mouse / Hover   │
│ 2. Advanced > HoverSync │  ───► │ 2. Role: Source         │  ───► │ 2. Targets Synchronize  │
│ 3. Role: Target         │       │ 3. Add Layer: #card-img │       │ 3. 60FPS Zero-Lag FX    │
│ 4. Alias: card-img      │       │ 4. Set Scale & Duration │       │                         │
└─────────────────────────┘       └─────────────────────────┘       └─────────────────────────┘
```

1. **Step 1: Set Up the Target (Receiver)**
   - Click on the widget you want to animate (e.g. an Image widget).
   - Navigate to **Advanced > HoverSync Pro > Enable**.
   - Set **Interaction Role** to `Passive Receiver (Target)`.
   - Set **Custom Element Alias** to a unique name: `product-hero-image` (no spaces or `#`).

2. **Step 2: Set Up the Source (Trigger)**
   - Click on the widget you want to hover or click (e.g. a Button widget).
   - Navigate to **Advanced > HoverSync Pro > Enable**.
   - Set **Interaction Role** to `Primary Trigger (Source)`.
   - Choose **Trigger Event**: `On Hover`, `On Click`, or `On Scroll`.
   - In **Effects Layer Manager**, click **Add Effect**:
     - **Target CSS Selector**: `#product-hero-image` (must start with `#`).
     - **Effect Type**: Choose `Scale / Zoom`.
     - **Value**: `1.15`
     - **Duration**: `400ms`
     - **Easing**: `GSAP Power4 (Ultra Smooth)`.

3. **Step 3: Test in Live Editor Preview**
   - Toggle **Live Editor Preview** ON inside the Elementor panel. Test the interaction immediately inside the builder canvas without needing to reload or open a new tab!

---

## 💡 Real-World Use Cases & Code Recipes

### 1. E-Commerce Product Card Sync
Hovering on the card scales the image, inflates the discount badge, highlights the price tag, and slides up the "Quick Add" button.

```html
<!-- Trigger Card Wrapper -->
<div id="product-card-1"
     data-hoversync='{
       "id": "product-card-1",
       "role": "source",
       "event": "hover",
       "fx": [
         { "selector": "#p1-img", "type": "scale", "val": 1.15, "dur": 450, "ease": "power3.out" },
         { "selector": "#p1-img", "type": "rotate", "val": "-4deg", "dur": 450, "ease": "power2.out" },
         { "selector": "#p1-badge", "type": "scale", "val": 1.2, "dur": 350, "ease": "back.out" },
         { "selector": "#p1-price", "type": "color", "val": "#a855f7", "dur": 300 },
         { "selector": "#p1-btn", "type": "translateY", "val": "0px", "dur": 400, "ease": "power3.out" },
         { "selector": "#p1-btn", "type": "opacity", "val": 1, "dur": 300 }
       ]
     }'
     class="product-card">
  
  <div class="product-media">
    <span id="p1-badge" class="badge">HOT SALE -30%</span>
    <div id="p1-img" class="product-visual">🎧</div>
    <button id="p1-btn" class="quick-add-btn">Quick Add to Cart</button>
  </div>

  <h4>Acoustic Pro Wireless</h4>
  <div id="p1-price" class="price">$189.00</div>
</div>
```

---

### 2. SaaS Pricing Tier Sibling Dimming
Hovering on one pricing plan highlights it with a 1.06x scale and neon glow while dimming non-active competitor plans to 35% opacity.

```html
<!-- Pro Plan Trigger -->
<div id="plan-pro"
     data-hoversync='{
       "id": "plan-pro",
       "role": "source",
       "event": "hover",
       "fx": [
         { "selector": "#plan-starter", "type": "opacity", "val": 0.35, "dur": 300 },
         { "selector": "#plan-enterprise", "type": "opacity", "val": 0.35, "dur": 300 },
         { "selector": "#plan-pro", "type": "scale", "val": 1.06, "dur": 350, "ease": "back.out" },
         { "selector": "#plan-pro", "type": "boxShadow", "val": "0 25px 50px rgba(147, 51, 234, 0.45)", "dur": 300 }
       ]
     }'
     class="pricing-tier featured">
  <span class="tag">MOST POPULAR</span>
  <h3>Professional</h3>
  <div class="price">$49/mo</div>
  <button class="tier-btn">Get Pro Access</button>
</div>
```

---

## 🏷️ Button & UI Typography Standards

To guarantee crisp, legible, and professional typography across both light and dark Elementor themes, all interactive buttons in HoverSync Pro adhere to a strict **`font-weight: 600`** standard:

```css
/* Universal Button Weight Standard */
button,
.hs-btn,
.elementor-button,
input[type="button"],
input[type="submit"],
[role="button"] {
  font-weight: 600 !important;
  letter-spacing: 0.01em;
}
```

---

## 📂 Plugin File Structure

```
hoversync-elementor-hover-effects/
├── hoversync-elementor-hover-effects.php  # Main Plugin Bootstrap & Lifecycle
├── includes/
│   ├── class-database.php               # Effect Meta DB Storage & Versioning
│   ├── class-controls.php               # Elementor Panel Controls & Repeaters
│   ├── class-renderer.php               # Front-End Data & HTML Output Processing
│   ├── class-ajax-handler.php           # Nonce-Secured Admin AJAX Interface
│   └── class-settings.php               # Global Performance & Script Enqueue Settings
├── assets/
│   ├── js/
│   │   ├── frontend.js                  # Zero-Lag Engine (v1.2.0 Vanilla JS)
│   │   └── editor.js                    # MutationObserver Live Editor Bridge
│   └── css/
│       ├── frontend.css                 # Hardware Accelerated GPU Classes
│       └── editor.css                   # High-Contrast Bento-Style Editor UI
├── LICENSE                              # GPL v2 or later
└── README.md                            # Complete Technical Documentation
```

---

## 🧩 Developer API & Hooks

### JavaScript Frontend Events
Listen for trigger and reversal lifecycle events in your custom JavaScript:

```javascript
// Target element effect activated
document.addEventListener('hoversync:activate', (event) => {
  const { sourceId, targetSelector, effects } = event.detail;
  console.log(`Trigger [${sourceId}] activated targets:`, targetSelector);
});

// Target element effect reversed
document.addEventListener('hoversync:reverse', (event) => {
  const { sourceId, targetSelector } = event.detail;
  console.log(`Trigger [${sourceId}] reversed.`);
});
```

### PHP Filters
Extend or sanitize effect payloads before frontend output:

```php
add_filter('hoversync_pro/render_config', function($config, $post_id, $widget_id) {
    // Modify configuration dynamically
    $config['debug'] = WP_DEBUG;
    return $config;
}, 10, 3);
```

---

## 📋 Changelog

### v1.2.0 — Codename: "The Marionettist" (Current Release)
- **New**: IntersectionObserver-based Scroll & Viewport Sync with 3 directional reverse modes (`Reverse on Scroll Up`, `Reverse on Exit`, `Never Reverse`).
- **New**: Multi-Layer Effect Stacking with independent durations, delays, and GSAP-like easing curves.
- **New**: Custom CSS Declaration Repeater field for animating arbitrary CSS rules.
- **New**: Responsive device scoping (`All Devices`, `Desktop Only >1024px`, `Mobile Only <=1024px`).
- **Update**: Standardized all plugin buttons and CTA controls to `font-weight: 600`.
- **Optimization**: Zero layout thrashing DOM write batching with automatic `will-change` garbage collection.
- **Fix**: Resolved sticky hover state on iOS Safari and Android Chrome touch devices.

### v1.1.0
- Added Click / Toggle State interaction mode.
- Added live preview bridge for the Elementor Editor panel.
- Added Glass Blur, Grayscale, and Brightness filters.

### v1.0.0
- Initial Release of HoverSync for Elementor.


---

## 💡 Ideas & Plans
### v1.3.0
- We should introduce custom JavaScript snippet features similar to CSS....

## 👨‍💻 Authors & Credits

- **Architect & Lead Developer**: LM Designers x DrSmoK3y x Creativators
- **Ecosystem**: Built for the WordPress & Elementor Community
- **Inspiration**: The art of marionette puppetry from Lord of Mysteries

---

## 📜 License

This project is licensed under the **GNU General Public License v2.0 or later** (GPL-2.0).  
Feel free to use, modify, and distribute for personal and commercial client websites.
