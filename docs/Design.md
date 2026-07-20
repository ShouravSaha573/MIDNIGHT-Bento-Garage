# Design

## 1. Design Name

**Sunrise Coral Glass**

The interface combines:

- a warm sunrise palette;
- white frosted-glass bento cards;
- coral primary actions;
- cyan informational accents;
- dark readable typography;
- clean automotive imagery;
- soft shadows instead of neon glow.

The design must feel:

- warm;
- bright;
- premium;
- clear;
- reliable;
- automotive;
- modern but not crowded.

---

## 2. Visual Reference Interpretation

The supplied visual boards cover:

1. Main Sunrise Coral Glass homepage
2. User booking experience
3. Admin dashboard
4. Responsive desktop/tablet/mobile layouts
5. Design system
6. Components and interaction states

The images define the intended visual language, not the final required data model.

Features seen in the images such as service type, time, estimated cost, revenue, ratings, and reports are optional display concepts. The assignment-required form fields and business rules take priority.

---

## 3. Core Design Principles

### 3.1 Clarity first

- dark text on bright surfaces;
- visible labels;
- generous whitespace;
- readable table;
- obvious primary action;
- exact availability text.

### 3.2 Warm automotive personality

- cream and peach create warmth;
- coral creates energy and action;
- car imagery maintains workshop identity;
- dark charcoal prevents the interface from feeling childish.

### 3.3 Controlled glass

- glass is used for main bento containers;
- form inputs remain more solid;
- tables remain nearly solid;
- transparency never reduces readability.

### 3.4 Bento organization

Information is divided into purposeful cards:

- hero;
- capacity;
- form;
- availability;
- help;
- summary;
- admin table;
- edit panel.

Do not create extra cards only for decoration.

### 3.5 Consistency

The same colors, spacing, radius, and state rules must be used on both User and Admin Panels.

---

## 4. Color Palette

```css
:root {
    --cream-background: #FFF7F2;
    --soft-peach: #FFEDE4;
    --peach-deep: #FFD7C8;

    --glass-white: rgba(255, 255, 255, 0.72);
    --glass-strong: rgba(255, 255, 255, 0.90);
    --solid-white: #FFFFFF;

    --coral-primary: #FF6A5B;
    --coral-hover: #F85A4C;
    --coral-deep: #E74D3C;
    --coral-light: #FFD7CF;

    --cyan-accent: #19C7D8;
    --cyan-deep: #089FB0;
    --cyan-light: #DDF8FB;

    --success-green: #22C55E;
    --success-light: #EAF9EF;

    --warning-amber: #F59E0B;
    --warning-light: #FFF6DF;

    --danger-red: #EF4444;
    --danger-light: #FFF0F0;

    --charcoal-text: #0F172A;
    --secondary-text: #475569;
    --muted-text: #64748B;
    --border: #E6EAF0;
}
```

---

## 5. Color Usage

### Coral

Use for:

- Book Appointment;
- Confirm;
- Save Changes;
- active navigation;
- selected mechanic;
- important section accent;
- focus ring.

Do not use coral for every icon and paragraph.

### Cyan

Use for:

- information icons;
- step numbers;
- help;
- secondary links;
- View Profile if kept;
- neutral progress information.

### Green

Use for:

- available;
- success;
- valid field;
- operational state.

### Amber

Use for:

- one slot remaining;
- warning;
- caution.

### Red

Use for:

- fully booked;
- blocking validation;
- duplicate/error state.

### Charcoal

Use for:

- heading;
- label;
- body;
- table;
- important metrics.

---

## 6. Page Background

```css
body {
    color: var(--charcoal-text);
    background:
        radial-gradient(
            circle at 10% 10%,
            rgba(255, 185, 160, 0.34),
            transparent 32%
        ),
        radial-gradient(
            circle at 90% 15%,
            rgba(25, 199, 216, 0.11),
            transparent 30%
        ),
        linear-gradient(135deg, #FFF7F2, #FFF1EA);
}
```

Rules:

- no heavy repeating pattern;
- no dark full-page background;
- no more than three decorative gradient areas;
- keep form background calm;
- coral wave at bottom may be implemented with a pseudo-element or SVG.

---

## 7. Glass System

### Standard card

```css
.glass-card {
    background: rgba(255, 255, 255, 0.72);
    border: 1px solid rgba(255, 255, 255, 0.82);
    -webkit-backdrop-filter: blur(18px);
    backdrop-filter: blur(18px);
    border-radius: 20px;
    box-shadow: 0 14px 38px rgba(71, 45, 39, 0.10);
}
```

### Strong card

```css
.strong-card {
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(255, 255, 255, 0.96);
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
}
```

Use strong cards for:

- appointment summary;
- admin table;
- edit form fields;
- important error/success content.

### Fallback

Browsers without backdrop blur must still show a readable white card. Therefore the background color must already be sufficiently opaque.

---

## 8. Typography

### Font choice

Preferred:

```css
font-family: "Inter", Arial, Helvetica, sans-serif;
```

If external fonts are avoided:

```css
font-family: Arial, Helvetica, sans-serif;
```

Use one font family across the project.

### Type scale

```css
.hero-title {
    font-size: clamp(2.25rem, 5vw, 4rem);
    line-height: 1.08;
    font-weight: 750;
    letter-spacing: -0.03em;
}

h1 {
    font-size: clamp(2rem, 4vw, 3rem);
    line-height: 1.15;
}

h2 {
    font-size: clamp(1.5rem, 3vw, 2rem);
    line-height: 1.25;
}

h3 {
    font-size: 1.25rem;
    line-height: 1.3;
}

body {
    font-size: 1rem;
    line-height: 1.6;
}

label {
    font-size: 0.875rem;
    font-weight: 650;
}
```

### Highlight text

```css
.highlight-text {
    background: linear-gradient(
        90deg,
        #F97316,
        #FF6A5B,
        #19C7D8
    );
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}
```

Use gradient only on a short hero phrase such as “Mechanic Online.”

---

## 9. Spacing

Use an 8-point-inspired system:

```text
4, 8, 12, 16, 24, 32, 40, 48, 64
```

Recommended:

```text
Page max width: 1240–1320px
Desktop side padding: 48–64px
Tablet side padding: 24–32px
Mobile side padding: 16px
Section gap: 48–64px
Card gap: 20–24px
Card padding: 24–32px
Form group gap: 16px
```

---

## 10. Radius

```css
:root {
    --radius-input: 11px;
    --radius-button: 11px;
    --radius-small-card: 15px;
    --radius-card: 20px;
    --radius-large: 24px;
    --radius-pill: 999px;
}
```

Do not mix many unrelated corner styles.

---

## 11. Shadow

```css
--shadow-card: 0 12px 32px rgba(15, 23, 42, 0.08);
--shadow-hover: 0 16px 34px rgba(15, 23, 42, 0.11);
--shadow-selected: 0 14px 30px rgba(255, 106, 91, 0.18);
--shadow-modal: 0 24px 60px rgba(15, 23, 42, 0.16);
```

Avoid neon glow.

---

## 12. User Panel Layout

### 12.1 Navbar

Desktop:

```text
Logo | Home | Book Appointment | Mechanics | Help | Admin | CTA
```

Style:

- glass-white background;
- sticky optional;
- 72–80px height;
- coral active underline;
- dark labels;
- coral CTA.

Mobile:

- logo;
- hamburger;
- collapsible menu;
- full-width CTA inside open menu.

### 12.2 Hero

Desktop grid:

```text
Hero text and car image: 2fr
Capacity card: 1fr
```

Hero content:

```text
DRIVE SAFE. WE’VE GOT YOU.
Book Your Trusted
Mechanic Online
Fast, reliable service with expert mechanics.
Book your appointment in minutes.
```

Image:

- white/silver car;
- bright workshop;
- warm light;
- optimized;
- no text embedded in image.

### 12.3 Main booking bento

Recommended desktop:

```text
Booking form: 38%
Mechanic availability: 62%
```

If the help card is beside mechanics:

```text
Form | Mechanics | Help
```

Only use three columns when text remains readable.

### 12.4 Help

Three numbered steps:

1. Fill in your details
2. Choose a date and mechanic
3. Confirm the appointment

Add a short duplicate/full explanation.

---

## 13. Appointment Form Design

### Form surface

- standard glass outer card;
- solid or 88% white fields;
- dark labels;
- border `#E2E8F0`;
- coral focus.

```css
.form-control {
    width: 100%;
    min-height: 46px;
    padding: 12px 14px;
    color: var(--charcoal-text);
    background: rgba(255, 255, 255, 0.90);
    border: 1px solid #E2E8F0;
    border-radius: var(--radius-input);
}

.form-control:focus {
    outline: none;
    border-color: var(--coral-primary);
    box-shadow: 0 0 0 3px rgba(255, 106, 91, 0.14);
}
```

### Required-field presentation

- label includes optional `*`;
- error below field;
- red border only while invalid;
- success border optional;
- do not replace labels with placeholders.

---

## 14. Mechanic Cards

Each card includes:

- initials/photo;
- name;
- optional role;
- exact free-place text;
- four slot dots;
- status text;
- selection indicator.

### Available

```css
.mechanic-card.available {
    background: rgba(255, 255, 255, 0.78);
    border: 1px solid rgba(34, 197, 94, 0.25);
}
```

### Selected

```css
.mechanic-card.selected {
    background: rgba(255, 238, 233, 0.92);
    border: 2px solid var(--coral-primary);
    box-shadow: var(--shadow-selected);
}
```

### Almost full

```css
.mechanic-card.almost-full {
    background: rgba(255, 248, 230, 0.92);
    border: 1px solid rgba(245, 158, 11, 0.50);
}
```

### Fully booked

```css
.mechanic-card.full {
    background: rgba(255, 240, 240, 0.92);
    border: 1px solid rgba(239, 68, 68, 0.42);
    opacity: 0.82;
    cursor: not-allowed;
}
```

### Four-dot convention

Choose one convention and keep it consistent.

Recommended:

- colored dots = booked slots;
- gray dots = free slots;
- accompanying text explicitly says free places.

Example:

```text
● ● ● ○
1 of 4 places available
```

Because dot interpretation may confuse users, the text is mandatory.

---

## 15. Workshop Capacity Card

Use a small CSS donut or simple progress bars.

Data shown:

- total workshop capacity for date;
- total booked;
- total free;
- number of full mechanics.

Do not hard-code values.

Color:

- free = green;
- booked = amber/coral;
- full warning = red.

The card should remain secondary to the booking form.

---

## 16. Button System

### Primary

```css
.btn-primary {
    min-height: 46px;
    padding: 0 20px;
    color: #FFFFFF;
    background: linear-gradient(135deg, #FF806F, #FF5B4D);
    border: 0;
    border-radius: var(--radius-button);
    font-weight: 700;
    box-shadow: 0 8px 20px rgba(255, 106, 91, 0.20);
}
```

### Hover

- translate up 1px;
- slightly deeper coral;
- no large scale animation.

### Pressed

- translate to 0;
- reduce shadow.

### Disabled

- 55% opacity;
- not-allowed cursor;
- remove hover movement.

### Secondary

- white/cyan-light surface;
- cyan or charcoal text;
- subtle border.

### Danger

Use only for destructive/blocking actions, not for “fully booked” viewing.

---

## 17. Alert and Feedback System

### Success

- green icon;
- light green surface;
- clear title;
- short explanation.

### Duplicate

- amber or red icon;
- state exact date conflict;
- tell client to choose another date.

### Full mechanic

- red icon;
- say mechanic is full;
- tell client to choose another mechanic or date.

### Toast behavior

- top-right desktop;
- top full-width mobile;
- visible 3–5 seconds;
- close button;
- not the only place where field errors appear.

---

## 18. Admin Design

### 18.1 Sidebar

- charcoal gradient;
- white text;
- coral logo/accent;
- coral active item;
- optional subtle car image at bottom.

Do not let the decorative car reduce navigation readability.

### 18.2 Main surface

- cream/peach background;
- glass summary cards;
- strong white table;
- elevated edit card.

### 18.3 Summary cards

Maximum four:

- total appointments;
- today’s appointments;
- free slots;
- full mechanics.

Use real data or omit the cards.

### 18.4 Appointment table

Design:

- near-solid white;
- dark text;
- light row separators;
- sticky header optional;
- small status pills only if status exists;
- clear Edit button;
- horizontal scroll on mobile.

Mandatory columns remain the priority.

### 18.5 Edit panel

- title and close;
- read-only client information;
- editable date;
- editable mechanic;
- Cancel and Save Changes;
- inline error area;
- coral Save button.

---

## 19. Responsive Design

### Desktop — 1200px+

- wide bento;
- hero in one row;
- form and mechanic cards side by side;
- admin sidebar visible;
- table and edit panel may share row.

### Tablet — 768px to 1199px

- hero text/image first;
- capacity below or beside;
- mechanic cards two per row;
- help beneath;
- admin sidebar collapses or becomes narrow;
- edit panel below table if necessary.

### Mobile — below 768px

- single column;
- 16px page padding;
- 100% buttons;
- card padding 18–20px;
- mechanic cards stacked;
- no tiny donut labels;
- admin table scrolls;
- modal nearly fills viewport.

### Small mobile — below 480px

- hero title smaller;
- navigation becomes menu;
- status text wraps;
- avoid side-by-side form fields;
- reduce decorative imagery.

---

## 20. Motion

Allowed:

- 150–250ms transitions;
- card lift up to 4px;
- selected check fade;
- toast slide/fade;
- button loading spinner.

Avoid:

- continuous glow;
- bouncing;
- rotating car;
- parallax;
- large page entrance animations;
- flashing status.

Respect reduced-motion preferences where practical.

---

## 21. Accessibility

- maintain strong contrast;
- use labels;
- visible focus;
- minimum 44px touch target;
- meaningful alt text;
- `aria-live` for result message if possible;
- text accompanies every color;
- disabled cards remain readable;
- error text is not tiny;
- keyboard can reach form and admin actions.

---

## 22. Image Rules

### Hero car

- one main image;
- bright workshop;
- white/silver vehicle;
- WebP;
- useful alt text;
- approximately 1200–1600px wide before optimization.

### Mechanic identity

Preferred simple implementation:

- circular initials;
- coral/cyan/charcoal backgrounds;
- no need to source five inconsistent portraits.

If photos are used:

- equal crop;
- same lighting style;
- optimized;
- descriptive or empty alt depending on whether the name already communicates identity.

### Logo

Use a simple local logo or CSS mark.

Do not rely on a generated poster image as the actual website interface.

---

## 23. Design Features to Exclude From Final Logic

Unless separately approved, exclude:

- appointment time;
- price;
- service type;
- ratings;
- reviews;
- revenue;
- reports;
- estimated duration;
- email field;
- My Bookings account;
- payment.

The final website should visually resemble the boards while using the assignment’s real fields.

---

## 24. Design Acceptance Checklist

- [ ] Sunrise Coral Glass theme is consistent.
- [ ] Required form fields are visible.
- [ ] Coral is reserved for important action.
- [ ] Cyan is secondary.
- [ ] Text remains dark and readable.
- [ ] Major cards use controlled glass.
- [ ] Table is nearly solid.
- [ ] Availability uses green/amber/red plus text.
- [ ] Selected mechanic is obvious.
- [ ] Full mechanic is disabled.
- [ ] Error messages are beside fields.
- [ ] Desktop, tablet, and mobile layouts work.
- [ ] No extra concept-image feature confuses assignment scope.
- [ ] Real website UI is built with HTML/CSS, not one flat image.
