# Memory

## Purpose

This file is the authoritative project memory for Midnight Bento Garage.

Use it to prevent future changes from contradicting the assignment, the selected design, or already locked technical decisions.

---

## 1. Fixed Assignment Facts

These facts must not be changed unless the instructor changes the assignment.

- Course: CSE 391 — Programming for the Internet.
- Assignment: Assignment 3 — PHP and MySQL Project.
- Product: car-workshop online appointment system.
- Required technology: HTML, CSS, JavaScript, PHP, MySQL.
- The project must be online and accessible by browser.
- The system has two parts: User Panel and Admin Panel.
- Workshop has approximately five senior mechanics.
- Maximum capacity is four active client cars per mechanic per day.
- Client must enter:
  - name;
  - address;
  - phone;
  - car license/registration number;
  - car engine number;
  - appointment date;
  - desired mechanic.
- Mechanic list must show free places.
- Client cannot take another appointment on the same date.
- Full mechanic must be rejected.
- Admin must see:
  - client name;
  - phone;
  - car registration;
  - appointment date;
  - mechanic name.
- Admin can change:
  - appointment date;
  - assigned mechanic.
- Basic help must be present.
- Data must be stored in a server-maintained database.

---

## 2. Locked Product Decisions

### Project name

```text
Midnight Bento Garage
```

### Design direction

```text
Sunrise Coral Glass
```

### Core user/date duplicate identity

```text
normalized phone + appointment date
```

Reason: the assignment does not provide a separate client account or client ID.

### Daily mechanic capacity

```text
4 appointments per mechanic per appointment date
```

### Initial mechanic count

Seed five mechanics because the assignment says approximately five.

### Initial mechanic names

```text
Alex Raj
Sam Michael
John Davis
Mike Chen
Ravi Sharma
```

These names are replaceable display data, not assignment facts.

### Engine number validation

Use practical alphanumeric validation:

```text
letters + digits + hyphen
```

The assignment’s numeric-only statement is an example. Explain the practical choice in viva.

### Past dates

Reject past dates.

### Admin login

Not required. Do not add unless explicitly requested later.

---

## 3. Locked Scope

### Must build

- user booking form;
- mechanic availability by date;
- free places out of four;
- server-side duplicate prevention;
- server-side capacity prevention;
- appointment insert;
- separate admin list;
- admin date update;
- admin mechanic update;
- help;
- responsive design;
- online deployment.

### Optional only

- search;
- date filter;
- admin summary cards;
- mechanic role;
- mechanic initials/photos;
- toast messages;
- CSS capacity visualization.

### Not in current scope

- time slots;
- service types;
- prices;
- payment;
- ratings;
- reviews;
- revenue;
- reports;
- email sending;
- SMS;
- client login;
- customer account;
- spare-parts management;
- inventory.

The generated design boards may show these, but they do not override scope.

---

## 4. Locked Design Memory

### Background

- cream + peach gradient;
- subtle coral sunrise shapes;
- limited cyan decorative glow;
- no dark full-page background.

### Primary color

```text
Coral #FF6A5B
```

### Secondary accent

```text
Cyan #19C7D8
```

### Semantic colors

```text
Success/Available: #22C55E
Warning/Almost Full: #F59E0B
Error/Fully Booked: #EF4444
```

### Text

```text
Main: #0F172A
Secondary: #475569
Muted: #64748B
```

### Cards

- frosted white;
- 72–90% opacity;
- blur approximately 18–20px;
- soft shadow;
- 18–24px radius;
- no excessive neon glow.

### Admin

- warm charcoal sidebar;
- bright glass main area;
- nearly solid white table;
- coral active navigation.

### Typography

- Inter preferred;
- Arial/Helvetica fallback;
- one font family;
- large readable headings;
- visible labels;
- no tiny text.

---

## 5. Locked Architecture Memory

### Architecture

```text
Browser
→ PHP pages/endpoints
→ PDO/MySQLi prepared statements
→ MySQL
```

### User routes

```text
index.php
get_availability.php
book_appointment.php
help.php or inline help
```

### Admin routes

```text
admin/index.php
admin/update_appointment.php
optional admin/get_appointment.php
optional admin/get_capacity.php
```

### Database tables

```text
mechanics
appointments
```

### Important constraints/indexes

```text
UNIQUE(phone, appointment_date)
INDEX(mechanic_id, appointment_date)
FOREIGN KEY appointments.mechanic_id → mechanics.id
```

### Booking order

```text
validate
→ normalize
→ verify mechanic
→ transaction
→ duplicate check
→ capacity check
→ insert
→ commit
```

### Admin update order

```text
load current appointment
→ validate target
→ transaction
→ duplicate check excluding current ID
→ capacity check excluding current ID
→ update
→ commit
```

---

## 6. File Structure Memory

```text
midnight-bento-garage/
├── index.php
├── book_appointment.php
├── get_availability.php
├── help.php
├── admin/
│   ├── index.php
│   ├── update_appointment.php
│   ├── get_appointment.php
│   └── get_capacity.php
├── config/
│   └── database.php
├── includes/
│   ├── functions.php
│   ├── header.php
│   ├── navbar.php
│   └── footer.php
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
└── database/
    └── garage_appointment.sql
```

This may be simplified, but responsibilities must remain readable.

---

## 7. UI Flow Memory

### Client

```text
Open page
→ fill required fields
→ choose date
→ availability loads
→ select free mechanic
→ submit
→ server checks
→ success or exact error
```

### Admin

```text
Open admin
→ view list
→ select edit
→ change date/mechanic
→ server checks
→ save or exact error
```

---

## 8. State Memory

### Mechanic states

```text
2–4 free: Available / green
1 free: Almost Full / amber
0 free: Fully Booked / red / disabled
Selected: coral border + check
```

### User feedback states

```text
success
duplicate
fully booked
validation error
network failure
database failure
loading
```

### Button states

```text
default
hover
pressed
disabled
loading
```

---

## 9. Security Memory

Always:

- prepared statements;
- output escaping;
- integer ID validation;
- server-side validation;
- safe error messages;
- transaction rollback;
- no credentials in browser;
- no raw exception shown.

Never:

- concatenate raw input into SQL;
- trust hidden fields;
- depend only on JavaScript;
- expose production credentials;
- show SQL errors to users.

---

## 10. AI Boundary Memory

AI is support, not runtime.

Allowed AI uses:

- explanation;
- planning;
- debugging;
- test suggestions;
- design brainstorming;
- code review.

Required human checks:

- understand every function;
- test every branch;
- remove unnecessary code;
- confirm requirement alignment;
- be able to explain in viva.

Do not add an AI API to the project.

---

## 11. Four-Phase Memory

### Phase 1

Foundation, database, static layout.

### Phase 2

User booking, availability, duplicate/capacity enforcement.

### Phase 3

Admin list and safe update.

### Phase 4

Final design, responsive QA, deployment.

Do not polish before core logic passes.

---

## 12. Testing Memory

Minimum test set:

### Booking

- all fields valid;
- empty fields;
- invalid phone;
- invalid engine;
- no date;
- past date;
- no mechanic;
- duplicate phone/date;
- mechanic at 0/1/2/3/4 bookings;
- fifth booking blocked.

### Admin

- edit date;
- edit mechanic;
- edit both;
- target full mechanic;
- target duplicate date;
- current record excluded;
- invalid appointment ID.

### Deployment

- public page;
- production insert;
- production admin list;
- production update;
- production duplicate rejection;
- production capacity rejection.

---

## 13. Viva Memory

The student should be able to explain:

### Why PHP

PHP receives forms, validates data, queries MySQL, and returns the result.

### Why JavaScript

JavaScript provides immediate validation and loads availability after date selection without requiring a full page refresh.

### Why MySQL

MySQL permanently stores mechanics and appointments and supports counting, duplicate checks, joins, and updates.

### Why server-side validation

Browser code can be bypassed. The server protects the database and enforces the real rules.

### Duplicate logic

Same normalized phone on the same date is treated as the same client/date appointment.

### Capacity logic

Count appointments for one mechanic on one date. Allow only when count is less than four.

### Admin update exclusion

The current appointment must not count against itself during duplicate or capacity checks.

### Glass design

Glass is used only on major cards. Table and inputs use stronger surfaces for visibility.

---

## 14. Progress Checklist

### Documentation

- [x] PRD defined
- [x] Architecture defined
- [x] Rules defined
- [x] Four phases defined
- [x] Design defined
- [x] Memory defined

### Build

- [ ] Folder structure created
- [ ] Database created
- [ ] Mechanics seeded
- [ ] User page shell created
- [ ] Availability endpoint created
- [ ] Booking process created
- [ ] Duplicate check tested
- [ ] Capacity check tested
- [ ] Admin list created
- [ ] Admin update created
- [ ] Responsive design completed
- [ ] Online deployment completed
- [ ] Final production tests passed

---

## 15. Change-Control Rule

When a future request changes the project:

1. check whether it conflicts with assignment facts;
2. check whether it expands scope;
3. update PRD if feature scope changes;
4. update Architecture if file/data flow changes;
5. update Rules if a new tool/library is introduced;
6. update Design if visual tokens change;
7. update Phases if delivery order changes;
8. update this Memory file last.

Never allow a concept image to override a mandatory assignment requirement.
