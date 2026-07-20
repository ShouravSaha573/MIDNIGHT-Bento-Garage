# Phases

## Project Delivery Plan

The project will be completed in exactly four phases.

Each phase has:

- objective;
- tasks;
- deliverables;
- tests;
- completion gate.

Do not begin optional features before the current phase passes its completion gate.

---

# Phase 1 — Foundation, Database, and Static Structure

## Objective

Create a stable project base, database schema, reusable design tokens, and non-functional page shells before implementing booking logic.

## 1.1 Project setup

Create the project folder and initial files:

```text
index.php
book_appointment.php
get_availability.php
admin/index.php
admin/update_appointment.php
config/database.php
includes/functions.php
assets/css/style.css
assets/css/admin.css
assets/js/booking.js
assets/js/admin.js
database/garage_appointment.sql
```

## 1.2 Local environment

- install/open XAMPP;
- start Apache and MySQL;
- place project inside `htdocs`;
- create database;
- import SQL;
- verify PHP can connect.

## 1.3 Database

Create:

- `mechanics`;
- `appointments`;
- foreign key;
- unique phone/date rule;
- mechanic/date index;
- seed approximately five mechanics.

## 1.4 Shared PHP utilities

Implement simple reusable functions:

```text
getDatabaseConnection()
normalizePhone()
validateDate()
escapeHtml()
mechanicExists()
```

## 1.5 Static User Panel shell

Build:

- navbar;
- hero;
- capacity-card placeholder;
- full required form;
- mechanic-card placeholder section;
- help section;
- footer.

Do not use fake form fields from the design board instead of required fields.

## 1.6 Static Admin Panel shell

Build:

- sidebar;
- heading;
- summary-card placeholders;
- appointment-table structure;
- edit panel/modal structure;
- capacity-card structure.

## 1.7 Base design system

Add CSS variables for:

- cream;
- soft peach;
- glass white;
- coral;
- cyan;
- green;
- amber;
- red;
- charcoal;
- muted gray;
- spacing;
- radius;
- shadows.

## Phase 1 deliverables

- working local project;
- database connection confirmed;
- mechanics visible from database;
- static responsive User and Admin shells;
- Sunrise Coral Glass base styling;
- SQL file.

## Phase 1 tests

- database imports without error;
- five mechanics exist;
- PHP connection succeeds;
- pages open without warnings;
- all required form fields are present;
- mobile page does not overflow except intended admin table scroll.

## Phase 1 completion gate

Proceed only when:

- database is real, not mocked;
- both pages render;
- folder structure is clean;
- no framework is installed;
- design tokens are defined centrally.

---

# Phase 2 — User Booking and Availability

## Objective

Complete the full client appointment process from date selection to approved database insertion.

## 2.1 Date handling

- set minimum date to current date;
- validate date in JavaScript;
- validate again in PHP;
- store as MySQL `DATE`.

## 2.2 Availability endpoint

Implement `get_availability.php`:

- accept selected date;
- validate date;
- load all active mechanics;
- count bookings per mechanic/date;
- calculate free places;
- return JSON.

## 2.3 Mechanic availability UI

Render for each mechanic:

- initials/photo;
- name;
- optional role;
- four slot dots;
- exact free-place text;
- status label;
- selectable state.

Rules:

```text
2–4 free → Available
1 free   → Almost Full
0 free   → Fully Booked and disabled
```

## 2.4 Form interaction

- date change triggers availability request;
- mechanic remains unselected until client chooses;
- selected card gets coral border and check icon;
- selected ID is stored in form field;
- full cards cannot be selected.

## 2.5 Client-side validation

Validate:

- name;
- address;
- phone;
- car registration;
- car engine number;
- date;
- mechanic.

Display field-level errors.

## 2.6 Server-side booking process

Implement:

1. receive POST;
2. trim and normalize;
3. validate every field;
4. verify mechanic;
5. start transaction;
6. duplicate phone/date check;
7. capacity check;
8. insert;
9. commit;
10. redirect/show success.

## 2.7 Feedback states

Implement:

- success;
- duplicate;
- full mechanic;
- invalid field;
- general failure;
- loading button;
- disabled button.

## 2.8 Help

Write real help content:

- how to book;
- how slots work;
- what duplicate means;
- what to do when a mechanic is full.

## Phase 2 deliverables

- complete User Panel;
- dynamic database availability;
- working booking insertion;
- duplicate prevention;
- capacity prevention;
- help;
- result messages.

## Phase 2 tests

### Validation

- submit empty form;
- invalid phone;
- invalid engine;
- past date;
- no mechanic.

### Duplicate

- first booking succeeds;
- same phone/date fails;
- same phone/different date succeeds.

### Capacity

- first four bookings for one mechanic/date succeed when clients differ;
- fifth booking fails;
- full mechanic becomes disabled after refresh.

### Data

- all required fields are stored correctly;
- admin database view confirms record.

## Phase 2 completion gate

Proceed only when:

- no mechanic can exceed four;
- no phone can book twice on the same date;
- checks are in PHP, not only JavaScript;
- success writes to MySQL;
- every error leaves database unchanged.

---

# Phase 3 — Admin Appointment Management

## Objective

Build the separate Admin Panel and allow safe date/mechanic changes.

## 3.1 Appointment list

Query appointments joined with mechanics.

Show:

- client name;
- phone;
- car registration;
- appointment date;
- mechanic name;
- edit action.

Optional detail fields may be available in edit view, not necessarily table columns.

## 3.2 Summary cards

If implemented, calculate real values:

- total appointments;
- today’s appointments;
- free slots for chosen date;
- fully booked mechanics.

Do not use fake hard-coded numbers.

## 3.3 Search and filter

Optional, simple features:

- search client/phone/registration;
- filter by date;
- filter by mechanic.

Core editing must work even if filters are omitted.

## 3.4 Edit interface

Open an edit panel/modal/page with:

- appointment ID hidden;
- client name read-only;
- phone read-only;
- car registration read-only;
- current date;
- current mechanic;
- date selector;
- mechanic selector;
- Cancel;
- Save Changes.

## 3.5 Admin update logic

Implement:

1. validate appointment ID;
2. load original appointment;
3. validate target date;
4. verify mechanic;
5. begin transaction;
6. duplicate check excluding current ID;
7. capacity check excluding current ID;
8. update;
9. commit;
10. refresh list and capacity.

## 3.6 Admin capacity display

For selected/edited date, show:

- mechanic;
- bookings out of four;
- free slots;
- color and text state.

## 3.7 Admin feedback

Implement:

- update success;
- full target mechanic;
- duplicate target date;
- invalid ID;
- general failure;
- save loading state.

## Phase 3 deliverables

- separate working Admin Panel;
- real appointment table;
- working edit interface;
- safe date update;
- safe mechanic update;
- capacity display;
- clear feedback.

## Phase 3 tests

- open every appointment;
- change only date;
- change only mechanic;
- change both;
- save no changes;
- move to full mechanic;
- move to date with same client’s other appointment;
- invalid appointment ID;
- verify database result;
- verify current record does not count against itself.

## Phase 3 completion gate

Proceed only when:

- table uses real database data;
- required columns are visible;
- valid changes persist;
- invalid changes never partially save;
- current appointment is excluded from update checks.

---

# Phase 4 — Final Design, Responsive QA, Deployment, and Submission

## Objective

Apply the complete Sunrise Coral Glass visual system, improve usability, test every rule, and deploy the final project online.

## 4.1 Final User Panel styling

Apply:

- cream/peach background;
- frosted navbar;
- bright car hero;
- coral primary actions;
- cyan information accents;
- glass form and availability containers;
- readable strong summary surfaces;
- green/amber/red mechanic states;
- subtle hover and loading states.

## 4.2 Final Admin styling

Apply:

- dark warm sidebar;
- coral active navigation;
- bright main background;
- glass summary cards;
- nearly solid table;
- elevated edit panel;
- capacity indicators;
- responsive table wrapper.

## 4.3 Responsive implementation

Test and fix:

- 1440px;
- 1200px;
- 992px;
- 768px;
- 480px;
- 360px.

Expected behavior:

- desktop bento;
- tablet reflow;
- mobile single column;
- admin horizontal table scroll;
- full-width mobile modal;
- no unreadable text.

## 4.4 Accessibility

Verify:

- labels connected to fields;
- keyboard focus;
- button height;
- alt text;
- contrast;
- color plus text;
- errors next to fields;
- active navigation understandable.

## 4.5 Performance

- convert hero image to optimized WebP;
- resize mechanic photos;
- remove unused CSS;
- remove unused JavaScript;
- avoid large libraries;
- cache only if simple;
- check page load on mobile connection.

## 4.6 Full regression test

Repeat all Phase 2 and Phase 3 tests.

Also test:

- page refresh after success;
- double-click submit;
- database offline behavior;
- availability request failure;
- browser back button;
- different browser;
- mobile orientation.

## 4.7 Production deployment

- create hosting database;
- import final SQL;
- upload files;
- set production credentials;
- verify public paths;
- verify image case sensitivity;
- create test booking;
- edit test booking;
- test duplicate;
- test full mechanic;
- confirm help page/section;
- remove debug output.

## 4.8 Submission package

Include:

- complete source folder;
- SQL file;
- public URL;
- any required declaration;
- short setup note if instructor requests it.

## Phase 4 deliverables

- final responsive visual design;
- accessibility and performance fixes;
- tested production site;
- SQL backup;
- clean source package;
- working public URL.

## Phase 4 completion gate

The project is finished only when:

- every assignment requirement works online;
- public site writes to production MySQL;
- admin update works online;
- required data is visible;
- duplicate and capacity rules work after deployment;
- no PHP warnings appear;
- no localhost dependency remains;
- code is understandable for viva.

---

# Final Phase Discipline

Build in this order:

```text
Correct database
→ correct booking rules
→ correct admin rules
→ polished design
→ deployment
```

Do not prioritize visual polish over server correctness.
