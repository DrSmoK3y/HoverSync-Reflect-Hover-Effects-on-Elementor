# HoverSync Pro for Elementor

**HoverSync Pro** transforms Elementor hover effects into a high-precision global interaction system. Trigger complex animations on any element from any source with zero coding, now featuring the **V2 HoverSync Engine**.

## 🚀 Key Features

### ⚡ V2 HoverSync Engine (New)
- **Combined Animations**: Scale, Rotate, Move, and Filter effects now work **simultaneously** on the same target.
- **Custom CSS Row**: Add any valid CSS properties (borderRadius, skew, border, etc.) directly in the effect repeater.
- **Improved Performance**: Ultra-lightweight logic with hardware-accelerated transitions.

### 🎯 Precision Targeting
- **Cross-Element Sync**: Hover on a Button, animate an Image half a page away.
- **Group Selectors**: Target multiple elements at once using CSS classes (e.g., `.product-card`).
- **Interaction Roles**: Define elements as **Sources**, **Targets**, or **Hybrids**.

### 🎨 Visual & Technical Power
- **Integrated Color Picker**: Smooth background color transitions with a professional picker.
- **Intensity Sliders**: Granular control over Blur, Opacity, and Grayscale.
- **Clean UI**: High-contrast labels designed for both Elementor Light and Dark modes.

---

## 🛠️ Effect Types

1.  **Transformations**: Scale (Zoom), Rotate, Translate X/Y (Move).
2.  **Filters**: Glass Blur, Opacity (Fade), Grayscale.
3.  **Styling**: Background Color (Solid/Alpha).
4.  **Custom**: Pure CSS properties (e.g., `clip-path`, `box-shadow`, `letter-spacing`).

---

## 📖 Quick Setup Guide

### 1. The Target (Receiver)
1. Select the element you want to animate.
2. Go to **Advanced > HoverSync Pro**.
3. Enable it and set role to `Passive Receiver (Target)`.
4. Set **Custom Element Alias** to something unique like `my-card-1`.

### 2. The Source (Trigger)
1. Select the element you want to hover over.
2. Go to **Advanced > HoverSync Pro**.
3. Enable it and set role to `Primary Trigger (Source)`.
4. Open the **Effects Layer Manager** and click **Add Effect**.
5. Set **Target CSS Selector** to `#my-card-1` (use `#` for IDs).
6. Configure your animation (e.g., Scale: 1.1, Duration: 400ms).

---

## 📝 Configuration Settings

- **Target Selector**: Can be an ID (`#id`), Class (`.class`), or generic HTML tag (`img`).
- **Effect Intensity**: Slider-based control for visual filters.
- **Duration**: Control speed in milliseconds (e.g., 300ms for fast, 800ms for slow).
- **Custom CSS Properties**: Enter rules like `border-radius: 20px; border: 2px solid purple;`.

---

## 📂 File Structure

```
hoversync-elementor-hover-effects/
├── hoversync-elementor-hover-effects.php (Main Plugin)
├── includes/
│   ├── class-database.php (Config Storage)
│   ├── class-controls.php (Elementor UI)
│   ├── class-renderer.php (Data Processing)
│   ├── class-ajax-handler.php (Interface)
│   └── class-settings.php (Global Controls)
├── assets/
│   ├── js/
│   │   └── frontend.js (V2 Engine)
│   └── css/
│       ├── frontend.css
│       └── editor.css (Premium UI Styling)
└── README.md
```

---

## 👨‍💻 Support & Documentation
- **Detailed Guide**: Check `how-2-use.md` for practical examples.
- **Compatibility**: Works with Elementor 3.x and WordPress 6.x.
- **Developer**: LM Designers x DrSmoK3y

---
*Built for Elementor Professionals who demand precision.*

## Credits

Created for Elementor page builder ecosystem
Built with performance and user experience in mind

## License

GPL v2 or later
