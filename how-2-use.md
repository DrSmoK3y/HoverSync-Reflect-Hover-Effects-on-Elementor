# HoverSync Pro: New & Improved Guide

HoverSync Pro has been simplified for maximum reliability. No more complex presets — just pure, fast interaction.

---

## 1. Interaction Roles
- **Primary Trigger (Source):** The element which, when hovered, starts the animation.
- **Passive Receiver (Target):** The element that responds to a trigger. (Custom Element Alias is configured here)
- **Hybrid (Both):** Can both trigger others and be triggered. (Custom Element Alias is configured here)

---

## 2. Fast Setup (The "Better Way")

### Step 1: Assign an ID (The Target)
Any element you want to animate should have a Custom ID.
1. Select the element (e.g., an Image).
2. Go to **Advanced > HoverSync Pro**.
3. Enable it and set role to `Passive Receiver (Target)`.
4. Set **Custom Element Alias** to `my-target` (unique name). Note: Custom Element Alias appears only when set to Target or Hybrid role.

### Step 2: Create the Link (The Source)
1. Select the trigger element (e.g., a Heading).
2. Go to **Advanced > HoverSync Pro**.
3. Enable it and set role to `Primary Trigger (Source)`.
4. In **Effects Layer Manager**, click **Add Effect**.
5. Set **Target CSS Selector** to `#my-target` (always use the `#` prefix for ID).
6. Pick an animation type (e.g., **Glass Blur** or **Scale**).

---

## 3. Custom CSS & Advanced Styling
You can now apply any CSS property to a target element.
1. In **Animation Type**, select `Custom CSS Properties`.
2. In the **CSS Properties** box, write your rules like this:
   `border-radius: 50%; border: 3px solid #ff0000; transform: skew(10deg);`
3. HoverSync will apply these properties on hover and reset them when the mouse leaves.

## 4. Combined Effects (Pro Feature)
You can **stack multiple effect layers**:
- Add one row for `Scale (Zoom)`.
- Add another row targeting the **same element** for `Rotation`.
- Result: The element will Scale AND Rotate simultaneously!

---

## 5. Why this is better?
- **Combined Animations:** Scale, Rotate, and Move work together seamlessly on the same target.
- **Color Picker:** Use the built-in color picker for background changes.
- **Pure CSS Power:** Use `Custom CSS Properties` to build any custom transition.
- **High Precision ID Targeting:** Target elements cleanly with unique `#` IDs.

---
**LM Designers x DrSmoK3y**
*High Precision Hover Syncing.*

