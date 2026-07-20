# Product Requirements Document (PRD)

## Project

**Project name:** Midnight Bento Garage  
**Course:** CSE 391 — Programming for the Internet  
**Assignment:** Assignment 3 — PHP and MySQL Project  
**Selected visual direction:** Sunrise Coral Glass  
**Product type:** Online car-workshop appointment system

---

## 1. Purpose of This Document

This document defines exactly what must be built, who will use it, what rules the system must enforce, which features are required, and which design-board elements are optional.

The assignment sheet is the authority for functionality. The supplied Sunrise Coral Glass text and images are the authority for visual direction only. When a design image contains features that the assignment does not require, those features must not silently become compulsory project logic.

---

## 2. Product Summary

Midnight Bento Garage is a browser-based appointment system for a car workshop.

A client can:

- enter personal and vehicle information;
- choose an appointment date;
- view the available capacity of mechanics for that date;
- select a desired mechanic;
- submit an appointment without visiting the workshop physically.

An admin can:

- view the appointment list;
- see the required client and appointment details;
- change an appointment date;
- change the assigned mechanic to another available mechanic.

The application must store data in a MySQL database on a server and must be deployed online for browser-based assessment.

---

## 3. Problem Statement

The workshop has approximately five senior mechanics. Each mechanic may handle a maximum of four active client cars per day.

Without an appointment system:

- clients may not receive their preferred mechanic;
- a mechanic may become overloaded;
- the manager must manually assign clients;
- conflicts and confusion may occur.

The system solves this by showing date-specific mechanic availability before booking and by enforcing capacity and duplicate-appointment rules on the server.

---

## 4. Target Users

### 4.1 Client

A person who wants to book a workshop appointment.

#### Client goals

- book quickly;
- select a preferred mechanic;
- know whether the mechanic has free places;
- receive a clear success or error message;
- avoid travelling to the workshop only to discover no availability.

### 4.2 Admin

The workshop manager or authorized workshop staff member.

#### Admin goals

- view all appointments clearly;
- identify each client and assigned mechanic;
- correct an appointment date;
- move a client to another available mechanic;
- avoid creating overbooking during an update.

---

## 5. Required Product Parts

The system must contain two working parts.

### 5.1 User Panel

The User Panel must provide:

- an appointment form;
- mechanic availability for the selected date;
- appointment submission;
- success, duplicate, validation, and fully-booked feedback;
- basic help information.

### 5.2 Admin Panel

The Admin Panel must provide:

- a separate working admin page;
- an appointment list;
- an edit action for a specific appointment;
- appointment-date modification;
- assigned-mechanic modification;
- capacity validation before an update is saved.

An admin login system is **not required by the assignment**. It may be added only if it remains simple and does not delay or break the required features.

---

## 6. Mandatory User Input Fields

The booking form must include all of the following:

1. Client name
2. Address
3. Phone number
4. Car license or registration number
5. Car engine number
6. Appointment date
7. Desired mechanic

All fields are required.

### 6.1 Field naming decision

Use the following clear database and UI names:

| Assignment wording | UI label | Database field |
|---|---|---|
| Name | Full Name | `client_name` |
| Address | Address | `address` |
| Phone | Phone Number | `phone` |
| Car License number | Car Registration Number | `car_license` |
| Car Engine Number | Car Engine Number | `car_engine` |
| Appointment date | Appointment Date | `appointment_date` |
| Desired mechanic | Select Mechanic | `mechanic_id` |

---

## 7. Mandatory Admin List Columns

The admin appointment table must display:

1. Client name
2. Phone
3. Car registration number
4. Appointment date
5. Mechanic name
6. Edit action

The following may also be shown because the database already stores them:

- address;
- car engine number;
- appointment ID;
- created date.

These additions must not make the table crowded or reduce readability.

---

## 8. Core Business Rules

### 8.1 Mechanic daily capacity

- Each mechanic has a maximum of **4 appointments per date**.
- Capacity is calculated separately for every mechanic and every appointment date.
- A mechanic with 4 bookings on a selected date is fully booked.
- A fully booked mechanic cannot be selected for a new appointment.
- The server must reject an appointment if the mechanic has reached capacity, even if the browser display is outdated.

### 8.2 Free-place calculation

For a selected date:

```text
free places = 4 - number of appointments for that mechanic on that date
```

Examples:

```text
0 bookings → 4 free places
1 booking  → 3 free places
2 bookings → 2 free places
3 bookings → 1 free place
4 bookings → 0 free places / fully booked
```

### 8.3 Duplicate appointment prevention

The assignment requires checking whether the client already has an appointment with any mechanic on the same date.

Because the assignment does not define a separate client ID, the implementation decision is:

```text
normalized phone number + appointment date = client/date identity
```

Therefore:

- the same phone number cannot create a second appointment on the same date;
- the mechanic does not matter for this duplicate check;
- changing to another mechanic on the same date does not create a second appointment;
- the duplicate rule must be checked on the server.

### 8.4 Admin update rule

When the admin changes a date or mechanic:

1. load the existing appointment;
2. validate the new date and mechanic;
3. check for another appointment for the same phone and target date;
4. exclude the current appointment from the duplicate check;
5. count target-mechanic bookings for the target date;
6. exclude the current appointment from capacity counting;
7. update only if the target mechanic still has capacity.

### 8.5 Database authority

JavaScript may improve the experience, but PHP and MySQL are the final authority for:

- duplicate checks;
- capacity checks;
- appointment insertion;
- appointment update;
- validation of submitted mechanic ID and date.

---

## 9. Functional Requirements

### FR-01 — Load mechanics

The system must load the mechanic list from the database.

### FR-02 — Date-specific availability

After a client selects a date, the system must show the free places for each mechanic on that date.

### FR-03 — Select mechanic

The client must be able to select one available mechanic.

### FR-04 — Disable full mechanic

A mechanic with zero free places must appear as fully booked and must not be selectable.

### FR-05 — Validate booking form

The system must validate all required fields before storing the appointment.

### FR-06 — Reject duplicate date booking

The system must reject a booking when the same normalized phone already has an appointment on the selected date.

### FR-07 — Reject mechanic overbooking

The system must reject a booking if the selected mechanic already has four appointments on that date.

### FR-08 — Save approved booking

A valid approved booking must be inserted into MySQL.

### FR-09 — Show result feedback

The client must see a clear message for:

- successful appointment;
- missing or invalid field;
- duplicate appointment;
- mechanic fully booked;
- server/database failure.

### FR-10 — Basic help

The User Panel must include basic guidance explaining:

- how to fill the form;
- how availability works;
- what a fully booked state means;
- what a duplicate appointment message means;
- how to select another date or mechanic.

### FR-11 — Admin appointment list

The Admin Panel must retrieve and display stored appointments with mechanic names.

### FR-12 — Open appointment edit form

The admin must be able to select a specific appointment and open its editable details.

### FR-13 — Change appointment date

The admin must be able to assign a new valid date.

### FR-14 — Change mechanic

The admin must be able to assign another mechanic who has capacity on the target date.

### FR-15 — Validate admin update

The server must prevent the admin from creating a duplicate client/date booking or overbooking a mechanic.

### FR-16 — Persist update

A valid admin change must be stored in MySQL and reflected in the appointment table.

### FR-17 — Online operation

The completed site and database must operate on an online PHP/MySQL server.

---

## 10. Validation Requirements

### 10.1 Client name

- required;
- trimmed;
- reasonable length, for example 2–100 characters;
- letters, spaces, apostrophes, periods, and hyphens may be accepted.

### 10.2 Address

- required;
- trimmed;
- maximum length controlled by database;
- must not contain only spaces.

### 10.3 Phone

- required;
- normalize by removing spaces, hyphens, and an optional leading plus for comparison;
- store a safe display value or normalized value consistently;
- numeric digits must remain after normalization;
- use a reasonable length such as 7–15 digits.

### 10.4 Car registration number

- required;
- trimmed;
- allow letters, numbers, spaces, and hyphens;
- convert to uppercase before storage if desired.

### 10.5 Car engine number

The assignment gives numeric-only validation as an example, but real engine numbers are often alphanumeric.

Final practical rule:

- required;
- allow letters, numbers, and hyphens;
- reject unsupported symbols;
- explain this decision in code comments and viva.

### 10.6 Appointment date

- required;
- must be a valid date;
- past dates should be rejected;
- database comparison must use `YYYY-MM-DD`.

### 10.7 Mechanic

- required;
- submitted mechanic ID must exist in the database;
- submitted mechanic must have fewer than four bookings on the date.

### 10.8 Dual validation

Validation must exist in:

- JavaScript for immediate client feedback;
- PHP for security and final correctness.

---

## 11. User Flow

```text
Open User Panel
      ↓
Read brief help/instructions
      ↓
Enter personal and car information
      ↓
Choose appointment date
      ↓
System requests date-specific availability
      ↓
Mechanic cards show free places
      ↓
Client selects an available mechanic
      ↓
Client submits form
      ↓
PHP validates all fields
      ↓
PHP checks duplicate phone + date
      ↓
PHP checks mechanic capacity
      ↓
Valid → save appointment → show success
Invalid → do not save → show exact error
```

---

## 12. Admin Flow

```text
Open Admin Panel
      ↓
Load appointment list from database
      ↓
Choose one appointment
      ↓
Open edit panel/modal/page
      ↓
Change date and/or mechanic
      ↓
Submit update
      ↓
PHP validates target date and mechanic
      ↓
Check duplicate excluding current appointment
      ↓
Check capacity excluding current appointment
      ↓
Valid → update record → refresh list
Invalid → keep original record → show exact error
```

---

## 13. Required UI Sections

### User Panel

- Sunrise Coral Glass navbar;
- hero introduction;
- workshop-capacity summary;
- appointment form;
- mechanic availability;
- help / How It Works;
- result feedback area;
- footer.

### Admin Panel

- contrasting dark warm sidebar or top navigation;
- page heading;
- summary cards, if implemented;
- appointment table;
- edit panel/modal;
- mechanic-capacity summary, if implemented;
- feedback messages.

---

## 14. Design-Board Features That Are Optional Only

The reference images contain attractive examples that are not required by the assignment:

- service type;
- appointment time;
- email;
- mechanic rating;
- mechanic expertise;
- vehicle model;
- estimated service time;
- estimated cost;
- revenue;
- reports;
- customer rating;
- My Bookings;
- payment;
- appointment status workflow.

These features must not be added to the required database unless there is a clear reason and enough time.

Recommended scope decision:

- do **not** add payment;
- do **not** add revenue;
- do **not** add customer ratings;
- do **not** add complex reports;
- do **not** add appointment time unless the instructor explicitly asks;
- use mechanic titles or initials only as simple display data if desired.

---

## 15. Non-Functional Requirements

### 15.1 Usability

- form labels must remain visible;
- error messages must identify the exact problem;
- availability must be understandable without guessing;
- mobile buttons must be easy to tap;
- admin information must be easy to scan.

### 15.2 Performance

- optimize hero and mechanic images;
- avoid large libraries;
- use simple SQL queries;
- avoid unnecessary repeated database requests;
- keep CSS and JavaScript small and readable.

### 15.3 Security

- use prepared statements;
- never trust browser validation alone;
- escape output shown in HTML;
- do not expose database passwords in public repositories;
- do not display raw database errors to users.

### 15.4 Accessibility

- maintain text contrast;
- include visible focus states;
- use labels connected to fields;
- use text and icons in addition to status colors;
- use semantic HTML;
- add alt text to meaningful images.

### 15.5 Maintainability

- use meaningful file and variable names;
- keep functions short;
- separate database connection, page logic, style, and scripts;
- avoid duplicated validation code where practical.

### 15.6 Deployment

- host PHP files and MySQL database online;
- update production database credentials safely;
- import the SQL schema;
- test all pages using the public URL.

---

## 16. Error Messages

Use clear messages such as:

```text
Please enter your full name.
Please enter a valid phone number.
Please enter a valid car registration number.
Please select an appointment date.
Please select an available mechanic.
You already have an appointment on this date.
This mechanic is fully booked on the selected date.
Appointment booked successfully.
The appointment was updated successfully.
The selected mechanic is no longer available. Please choose another mechanic.
Something went wrong. Please try again.
```

Do not show SQL, file paths, stack traces, or database credentials.

---

## 17. Acceptance Criteria

The project is complete only when all of the following are true.

### User Panel

- [ ] All seven required fields exist.
- [ ] Mechanics are loaded from MySQL.
- [ ] Availability changes according to selected date.
- [ ] Every mechanic shows free places out of four.
- [ ] Full mechanics cannot be selected.
- [ ] Empty and invalid input is rejected.
- [ ] Same phone cannot book twice on one date.
- [ ] A mechanic cannot exceed four bookings on one date.
- [ ] Valid appointment is stored in MySQL.
- [ ] Client sees a clear result message.
- [ ] Basic help is present.

### Admin Panel

- [ ] Admin page is separate and accessible.
- [ ] Appointment list comes from MySQL.
- [ ] Required columns are visible.
- [ ] A specific appointment can be edited.
- [ ] Appointment date can be changed.
- [ ] Mechanic can be changed.
- [ ] Update does not overbook a mechanic.
- [ ] Update does not create a duplicate client/date appointment.
- [ ] Valid update is persisted.

### Technical and submission

- [ ] HTML, CSS, JavaScript, PHP, and MySQL are used.
- [ ] Prepared statements are used.
- [ ] The design is responsive.
- [ ] The Sunrise Coral Glass design is applied consistently.
- [ ] The public website URL works.
- [ ] The online database connection works.
- [ ] No required feature depends only on fake/static data.

---

## 18. Requirement Priority

### Must Have

- required form fields;
- date-based mechanic availability;
- four-booking capacity limit;
- duplicate prevention;
- MySQL storage;
- separate admin list;
- admin date/mechanic update;
- validation;
- help;
- online deployment.

### Should Have

- mechanic capacity cards;
- clear toast/alert feedback;
- responsive layout;
- loading/disabled button states;
- admin summary cards;
- search or date filter.

### Could Have

- mechanic role/title;
- mechanic initials or photos;
- simple appointment detail view;
- lightweight admin filtering.

### Will Not Have Unless Explicitly Requested

- payment;
- revenue reports;
- ratings/reviews;
- service pricing;
- email delivery;
- SMS;
- complex login/roles;
- frameworks;
- external APIs.

---

## 19. Final Product Definition

Build a simple, student-readable, server-backed car-workshop appointment website with a warm Sunrise Coral Glass interface. The visual design may look modern, but the project logic must remain understandable, accurate, and directly aligned with the assignment.
