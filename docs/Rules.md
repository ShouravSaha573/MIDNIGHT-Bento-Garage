# Rules

## 1. Purpose

These rules keep the project aligned with the assignment, visually consistent with Sunrise Coral Glass, secure enough for a student web application, and simple enough to explain.

Rules are divided into:

- Must Use
- Must Do
- Should Do
- Must Avoid
- Error Handling
- Security Boundaries
- AI Boundaries
- Scope Boundaries
- Submission Rules

---

## 2. Must Use

The assignment explicitly requires:

- HTML5
- CSS3
- JavaScript
- PHP
- MySQL

Local development should use:

- XAMPP
- Apache
- MySQL or MariaDB
- phpMyAdmin

The final project must use a real server database. Required data must not exist only in JavaScript arrays or local storage.

---

## 3. Must Do

### 3.1 Required fields

Always include:

- name;
- address;
- phone;
- car registration/license number;
- car engine number;
- appointment date;
- desired mechanic.

### 3.2 Required panels

Always include:

- User Panel;
- separate Admin Panel;
- basic help;
- appointment list;
- admin date/mechanic editing.

### 3.3 Required logic

Always enforce on the server:

- one client appointment per phone/date;
- maximum four appointments per mechanic/date;
- valid existing mechanic;
- valid date;
- required input;
- safe admin update.

### 3.4 Required database behavior

- save approved appointments;
- read admin list from database;
- read mechanics from database;
- update edited appointment in database;
- use prepared statements;
- preserve referential integrity.

### 3.5 Required online behavior

- deploy pages online;
- deploy/import database online;
- verify all public routes;
- test using the production URL.

---

## 4. Should Do

- use semantic HTML;
- connect labels to inputs;
- set minimum date to today;
- normalize phone before duplicate comparison;
- convert car registration to uppercase;
- display four capacity indicators per mechanic;
- disable full mechanic cards;
- disable buttons while a request is being processed;
- show loading feedback;
- preserve valid form values after an error;
- use transactions for booking and admin update;
- keep functions short;
- comment business rules, not obvious syntax;
- make table horizontally scrollable on small screens.

---

## 5. Technology and Library Rules

### Allowed

- native HTML form elements;
- CSS Grid and Flexbox;
- CSS custom properties;
- CSS media queries;
- Vanilla JavaScript;
- `fetch()` for availability;
- PDO or MySQLi;
- small inline SVG icons;
- optimized local images;
- Google Fonts only if internet availability and assignment policy allow it.

### Avoid

- React
- Vue
- Angular
- Svelte
- Laravel
- Symfony
- Bootstrap
- Tailwind CSS
- jQuery
- chart libraries
- icon libraries that load hundreds of unused assets
- external booking APIs
- external database services unless the hosting specifically requires one
- code generators that produce architecture the student cannot explain

### Reason

The assignment tests direct understanding of HTML, CSS, JavaScript, PHP, and MySQL. Frameworks hide the required learning and make the project harder to explain.

---

## 6. Scope Rules

### Mandatory scope

Implement only what is necessary to satisfy:

- client booking;
- mechanic availability;
- duplicate prevention;
- capacity prevention;
- database persistence;
- admin list;
- admin update;
- help;
- responsive design;
- online deployment.

### Optional but safe

- admin summary cards;
- date filter;
- simple search;
- mechanic title;
- mechanic initials;
- toast messages;
- loading states;
- small capacity donut made with CSS.

### Do not implement unless specifically requested

- payment;
- estimated price;
- email sending;
- SMS;
- customer accounts;
- client login;
- ratings;
- review system;
- revenue;
- service inventory;
- spare parts;
- time-slot scheduling;
- complex appointment statuses;
- reports;
- analytics charts;
- staff payroll.

The design images may show these ideas, but they are visual examples, not compulsory requirements.

---

## 7. Data Rules

### Mechanics

- seed approximately five mechanics;
- each mechanic has a daily limit of four;
- mechanic names must come from the database;
- inactive/full mechanic cannot be used for a new appointment.

### Appointments

- every appointment references one mechanic;
- every appointment contains all required assignment data;
- same normalized phone/date combination must be unique;
- past appointment dates should not be accepted;
- raw form data must not be inserted directly.

### Phone

- trim;
- normalize to digits;
- validate reasonable length;
- use normalized value consistently for duplicate logic.

### Date

- use `YYYY-MM-DD` in PHP and MySQL;
- do not compare formatted visual dates;
- validate with server logic.

---

## 8. Validation Rules

### JavaScript validation

Use JavaScript for:

- empty fields;
- immediate phone feedback;
- date minimum;
- mechanic selection;
- button state;
- visual error placement.

### PHP validation

PHP must repeat every important validation.

Never assume JavaScript ran.

### Field error style

- place message directly below field;
- use red text and icon;
- keep the label visible;
- do not use browser alert for every field;
- clear the message when corrected.

---

## 9. Booking Logic Rules

The booking sequence must be:

```text
validate input
→ normalize data
→ verify mechanic
→ begin transaction
→ duplicate check
→ capacity check
→ insert
→ commit
```

Never:

```text
insert first
→ check later
```

Never use only the mechanic card’s displayed free count as proof of availability.

---

## 10. Admin Update Rules

- admin edits only a real appointment ID;
- load original appointment first;
- only date and mechanic need to be editable;
- duplicate check excludes current appointment ID;
- capacity check excludes current appointment ID;
- if update is invalid, do not partially change the record;
- display the reason;
- refresh table after success.

---

## 11. Error Handling Rules

### User-visible messages

Use understandable messages:

```text
Please enter a valid phone number.
You already have an appointment on this date.
This mechanic is fully booked.
The selected mechanic is no longer available.
Appointment booked successfully.
Appointment updated successfully.
Something went wrong. Please try again.
```

### Never show users

- SQL query text;
- database host;
- database password;
- stack trace;
- PHP warning path;
- server filesystem path;
- PDO exception message.

### Internal handling

- catch exceptions;
- rollback active transactions;
- log technical details on the server;
- return a generic safe error to the browser.

---

## 12. Security Rules

- use PDO/MySQLi prepared statements;
- escape every database value printed in HTML;
- validate IDs as integers;
- do not trust hidden inputs;
- verify mechanic exists;
- use POST for insert/update;
- do not put database credentials in JavaScript;
- do not commit production credentials publicly;
- use restrictive database user permissions where possible;
- use session-based CSRF protection if an admin login is later added;
- do not permanently delete appointments unless the assignment is changed.

---

## 13. Design Rules

### Must preserve

- Sunrise Coral Glass theme;
- cream and peach background;
- coral primary actions;
- cyan informational accent;
- dark charcoal text;
- white frosted glass major cards;
- soft shadows;
- bento organization;
- clear availability states.

### Glass rules

Use glass on major containers, not every element.

Do not:

- put tiny text over a transparent photo;
- use low-contrast white-on-white;
- use excessive blur;
- place busy patterns behind forms;
- apply glow to all cards.

### Status rules

Status must use color plus text:

```text
Green + “Available”
Amber + “Almost Full”
Red + “Fully Booked”
Coral border + “Selected”
```

### Admin table rule

The table should be almost solid white for readability, even though surrounding cards are glass.

---

## 14. Responsive Rules

### Desktop

- use wide bento layout;
- keep hero, capacity, form, and availability visible;
- use readable table width.

### Tablet

- move capacity under hero;
- show two mechanic cards per row;
- compact navigation;
- allow table scroll.

### Mobile

- single column;
- hamburger navigation;
- full-width inputs and buttons;
- stack mechanic cards;
- make edit panel nearly full-screen;
- maintain at least 44px touch targets.

Do not simply shrink desktop text until it becomes unreadable.

---

## 15. Coding Style Rules

### Naming

Use meaningful names:

```text
appointmentDate
selectedMechanicId
freePlaces
normalizePhone
getMechanicAvailability
```

Avoid:

```text
x
data1
abc
temp2
foo
```

### Functions

- one clear job per function;
- normally under 30–40 lines;
- avoid deep nesting;
- return early for invalid conditions;
- reuse phone/date validation.

### Comments

Good comment:

```php
// Exclude the current appointment so it does not count against itself.
```

Unhelpful comment:

```php
// Set variable.
```

### Formatting

- consistent indentation;
- one statement per line;
- no large commented-out blocks;
- no minified source files;
- no duplicate CSS rules without reason.

---

## 16. AI Boundaries

AI may be used for:

- understanding the assignment;
- brainstorming design;
- explaining PHP/MySQL logic;
- identifying bugs;
- suggesting test cases;
- reviewing accessibility;
- clarifying deployment steps.

AI must not be used to:

- add hidden requirements;
- fabricate working results;
- invent database data and present it as real;
- add libraries or frameworks against the rules;
- produce code the student cannot explain;
- bypass the assignment’s required technologies;
- create fake screenshots instead of a functioning site;
- replace server validation with visual-only behavior;
- write misleading AI-use declarations.

Before accepting AI-generated code, the student must:

1. read it;
2. understand each function;
3. test it;
4. remove unnecessary complexity;
5. confirm it follows the assignment;
6. be able to explain it in viva.

The final runtime application should not require an AI API.

---

## 17. Image and Asset Rules

- use one optimized hero car image;
- use WebP where practical;
- use descriptive alt text;
- do not use huge uncompressed PNG photos;
- use initials instead of mechanic photos if consistency is difficult;
- keep all local asset paths relative;
- verify production paths are case-sensitive.

---

## 18. Testing Rules

Test every core rule directly.

### Duplicate

- same phone + same date;
- same phone + different date;
- different phone + same date.

### Capacity

- mechanic with 0, 1, 2, 3, and 4 bookings;
- two near-simultaneous final-slot attempts if possible.

### Admin

- move to free mechanic;
- move to full mechanic;
- change date;
- no-change save;
- update to a date where same client has another appointment.

### Validation

- empty fields;
- whitespace only;
- invalid phone;
- invalid engine number;
- invalid mechanic ID;
- past date.

### Responsive

- desktop;
- tablet;
- 768px boundary;
- 480px;
- mobile landscape;
- horizontal table scroll.

---

## 19. Submission Rules

Before submission:

- deploy to online PHP/MySQL host;
- import final SQL;
- use production credentials;
- test from a different browser/device;
- confirm no localhost links;
- confirm Admin Panel is reachable;
- confirm user booking writes to production database;
- confirm admin update changes production data;
- remove debugging output;
- include required source files;
- preserve readable, standard code.

---

## 20. Final Boundary

A modern appearance must not turn the project into a large commercial platform.

The correct project is:

```text
small scope
+ complete required logic
+ clean Sunrise Coral Glass design
+ understandable PHP/MySQL code
+ real online operation
```
