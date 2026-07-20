# Architecture

## 1. Architecture Goal

The architecture must be simple enough to explain in a CSE 391 viva while still enforcing all booking rules correctly.

Use a traditional PHP server-rendered structure with small JSON endpoints where dynamic availability improves the user experience.

```text
Browser
  ├── HTML/PHP pages
  ├── CSS
  └── Vanilla JavaScript
          ↓ HTTP requests
PHP application logic
          ↓ prepared SQL
MySQL database
```

The server is authoritative. JavaScript never has the final decision about booking approval.

---

## 2. Technology Stack

### Frontend

- HTML5
- CSS3
- Vanilla JavaScript
- Native form controls
- Optional inline SVG or simple CSS icons

### Backend

- PHP 8.x
- PDO recommended, or MySQLi with prepared statements

### Database

- MySQL or MariaDB

### Local environment

- XAMPP
- Apache
- PHP
- MySQL
- phpMyAdmin

### Production

- PHP/MySQL-compatible online hosting
- public browser-accessible URL
- production database credentials stored outside public output

---

## 3. Recommended Architectural Style

Use a small three-layer structure.

### Presentation layer

Responsible for:

- page markup;
- form fields;
- mechanic cards;
- admin table;
- messages;
- responsive layout.

Files include:

- `index.php`
- `help.php`
- `admin/index.php`
- CSS and JavaScript assets.

### Application layer

Responsible for:

- validation;
- phone normalization;
- availability calculation;
- duplicate detection;
- capacity enforcement;
- booking insertion;
- admin update.

Files include:

- `book_appointment.php`
- `get_availability.php`
- `admin/update_appointment.php`
- small reusable PHP functions.

### Data layer

Responsible for:

- database connection;
- mechanics data;
- appointments data;
- prepared SQL queries;
- schema and seed data.

Files include:

- `config/database.php`
- `database/garage_appointment.sql`.

---

## 4. Website Route Map

```text
/
├── index.php
│   User Panel and booking form
│
├── get_availability.php?date=YYYY-MM-DD
│   Returns date-specific mechanic availability as JSON
│
├── book_appointment.php
│   Receives POST booking submission
│
├── help.php
│   Basic help information, or help section may remain in index.php
│
└── admin/
    ├── index.php
    │   Appointment list and admin interface
    │
    ├── get_appointment.php?id=...
    │   Optional JSON endpoint for edit details
    │
    ├── get_capacity.php?date=YYYY-MM-DD
    │   Optional admin capacity endpoint
    │
    └── update_appointment.php
        Receives POST admin update
```

`get_appointment.php` and `get_capacity.php` are optional if the necessary information is already rendered on the admin page.

---

## 5. Recommended Folder and File Structure

```text
midnight-bento-garage/
│
├── index.php
├── book_appointment.php
├── get_availability.php
├── help.php
│
├── admin/
│   ├── index.php
│   ├── get_appointment.php
│   ├── get_capacity.php
│   └── update_appointment.php
│
├── config/
│   └── database.php
│
├── includes/
│   ├── functions.php
│   ├── header.php
│   ├── navbar.php
│   └── footer.php
│
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   ├── admin.css
│   │   └── responsive.css
│   │
│   ├── js/
│   │   ├── booking.js
│   │   └── admin.js
│   │
│   └── images/
│       ├── hero-car.webp
│       ├── logo.png
│       └── mechanic-placeholder.webp
│
├── database/
│   └── garage_appointment.sql
│
└── README.md
```

### Simpler accepted structure

If the above feels too large, combine:

- `responsive.css` into `style.css`;
- `admin.css` into `style.css`;
- common header/footer directly into pages;
- admin capacity data directly in `admin/index.php`.

Do not combine everything into one unreadable file.

---

## 6. Page Architecture

### 6.1 User Panel — `index.php`

Server-side responsibilities:

- connect to database;
- load mechanics;
- choose a default availability date or show unselected state;
- render any flash success/error message.

Frontend sections:

```text
Navbar
Hero + car image + capacity summary
Appointment form
Mechanic availability
Help / How It Works
Footer
```

### 6.2 Availability endpoint — `get_availability.php`

Input:

```text
GET date=YYYY-MM-DD
```

Output example:

```json
{
  "success": true,
  "date": "2026-07-20",
  "mechanics": [
    {
      "id": 1,
      "name": "Alex Raj",
      "booked": 1,
      "free": 3,
      "is_full": false
    }
  ]
}
```

Responsibilities:

- validate date;
- query all active mechanics;
- count appointments for the date;
- calculate `free = 4 - booked`;
- never return a negative free count;
- return JSON with correct content type.

### 6.3 Booking endpoint — `book_appointment.php`

Input:

```text
POST client_name
POST address
POST phone
POST car_license
POST car_engine
POST appointment_date
POST mechanic_id
```

Responsibilities:

- trim and validate input;
- normalize phone;
- verify mechanic exists;
- reject past/invalid date;
- begin transaction;
- check duplicate client/date;
- check mechanic/date count;
- insert if both checks pass;
- commit;
- return or redirect with a safe result message.

### 6.4 Admin list — `admin/index.php`

Responsibilities:

- load appointments joined with mechanics;
- show required columns;
- provide edit action;
- optionally load summary counts;
- optionally filter by date or search text.

### 6.5 Admin update — `admin/update_appointment.php`

Input:

```text
POST appointment_id
POST appointment_date
POST mechanic_id
```

Responsibilities:

- load current appointment;
- validate target date;
- verify target mechanic;
- begin transaction;
- duplicate check excluding current appointment;
- capacity check excluding current appointment;
- update record;
- commit;
- return safe success or error feedback.

---

## 7. Database Design

### 7.1 Mechanics table

```sql
CREATE TABLE mechanics (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role_title VARCHAR(100) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

Only `id` and `name` are strictly necessary. `role_title` and `is_active` are small, useful additions.

### 7.2 Appointments table

```sql
CREATE TABLE appointments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    car_license VARCHAR(50) NOT NULL,
    car_engine VARCHAR(50) NOT NULL,
    appointment_date DATE NOT NULL,
    mechanic_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_appointments_mechanic
        FOREIGN KEY (mechanic_id)
        REFERENCES mechanics(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT uq_client_date
        UNIQUE (phone, appointment_date),

    INDEX idx_mechanic_date (mechanic_id, appointment_date),
    INDEX idx_appointment_date (appointment_date)
);
```

### 7.3 Why use a unique client/date constraint

Application checks provide a friendly message, but the unique constraint protects the database from race conditions and accidental duplicate requests.

The phone value used in this constraint must be normalized consistently before storage.

### 7.4 Mechanic seed data

```sql
INSERT INTO mechanics (name, role_title) VALUES
('Alex Raj', 'Senior Mechanic'),
('Sam Michael', 'Auto Specialist'),
('John Davis', 'Lead Technician'),
('Mike Chen', 'Hybrid Specialist'),
('Ravi Sharma', 'Diagnostics Expert');
```

The names are design decisions, not assignment requirements.

---

## 8. Data Relationships

```text
mechanics
  id ───────────────┐
                    │ one mechanic
                    │ has many appointments
                    ↓
appointments
  mechanic_id
```

Every appointment must reference exactly one valid mechanic.

---

## 9. Availability Query

Use a left join so mechanics with zero bookings are also returned.

```sql
SELECT
    m.id,
    m.name,
    m.role_title,
    COUNT(a.id) AS booked_count,
    GREATEST(4 - COUNT(a.id), 0) AS free_places
FROM mechanics m
LEFT JOIN appointments a
    ON a.mechanic_id = m.id
   AND a.appointment_date = :appointment_date
WHERE m.is_active = 1
GROUP BY m.id, m.name, m.role_title
ORDER BY m.id;
```

Display logic:

```text
free_places >= 2 → Available / green
free_places = 1  → Almost Full / amber
free_places = 0  → Fully Booked / red / disabled
```

---

## 10. Booking Transaction

A robust but understandable booking sequence:

```php
$pdo->beginTransaction();

try {
    // 1. Duplicate check.
    // 2. Capacity check.
    // 3. Insert appointment.
    // 4. Commit.
} catch (Throwable $error) {
    $pdo->rollBack();
    // Log internally and show safe message.
}
```

### 10.1 Duplicate query

```sql
SELECT COUNT(*)
FROM appointments
WHERE phone = :phone
  AND appointment_date = :appointment_date;
```

### 10.2 Capacity query

```sql
SELECT COUNT(*)
FROM appointments
WHERE mechanic_id = :mechanic_id
  AND appointment_date = :appointment_date;
```

Approval:

```text
duplicate count = 0
AND capacity count < 4
```

### 10.3 Insert query

```sql
INSERT INTO appointments (
    client_name,
    address,
    phone,
    car_license,
    car_engine,
    appointment_date,
    mechanic_id
) VALUES (
    :client_name,
    :address,
    :phone,
    :car_license,
    :car_engine,
    :appointment_date,
    :mechanic_id
);
```

---

## 11. Admin Update Transaction

### 11.1 Load existing appointment

```sql
SELECT *
FROM appointments
WHERE id = :appointment_id;
```

### 11.2 Duplicate check excluding current record

```sql
SELECT COUNT(*)
FROM appointments
WHERE phone = :phone
  AND appointment_date = :appointment_date
  AND id <> :appointment_id;
```

### 11.3 Capacity check excluding current record

```sql
SELECT COUNT(*)
FROM appointments
WHERE mechanic_id = :mechanic_id
  AND appointment_date = :appointment_date
  AND id <> :appointment_id;
```

### 11.4 Update query

```sql
UPDATE appointments
SET appointment_date = :appointment_date,
    mechanic_id = :mechanic_id
WHERE id = :appointment_id;
```

---

## 12. Concurrency and Correctness

Two clients could see the same final free slot and submit almost simultaneously.

Minimum acceptable protection:

- transaction;
- duplicate unique constraint;
- capacity recheck immediately before insert;
- disable repeated submit button.

Stronger optional protection:

- lock the target mechanic row with `SELECT ... FOR UPDATE` during the capacity check.

For this student project, a clear transaction and immediate server-side recheck are sufficient if implemented correctly.

---

## 13. JavaScript Architecture

### `booking.js`

Functions should remain small and understandable:

```text
setMinimumDate()
loadAvailability(date)
renderMechanics(mechanics)
selectMechanic(mechanicId)
validateBookingForm()
setBookingButtonLoading(isLoading)
showMessage(type, message)
```

Recommended behavior:

1. set date input minimum to today;
2. listen to appointment-date change;
3. call `get_availability.php`;
4. update mechanic cards and hidden/select field;
5. block full cards;
6. validate before submit;
7. disable button during submission.

### `admin.js`

Possible functions:

```text
openEditModal(appointmentData)
closeEditModal()
loadAdminCapacity(date)
validateAdminUpdate()
setSaveButtonLoading(isLoading)
```

Avoid advanced classes, state libraries, and complex component patterns.

---

## 14. Server Response Strategy

Two simple choices are acceptable.

### Option A — Traditional form and redirect

- submit form;
- PHP processes;
- redirect back with session/URL message;
- page renders result.

Advantages:

- easiest to explain;
- reliable;
- works without AJAX.

### Option B — Fetch and JSON

- JavaScript submits with `fetch`;
- PHP returns JSON;
- interface updates without full reload.

Advantages:

- smoother design.

Recommended compromise:

- use `fetch` only for date availability;
- use a normal POST for booking and admin update.

This preserves simplicity while meeting the “communicate at every step” requirement.

---

## 15. Output Escaping

Every value printed into HTML must be escaped:

```php
<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>
```

This applies to:

- client names;
- addresses;
- phone numbers;
- car numbers;
- mechanic names;
- error values echoed back into the form.

---

## 16. Phone Normalization

Use one reusable function:

```php
function normalizePhone(string $phone): string
{
    return preg_replace('/\D+/', '', $phone);
}
```

Then validate length and store the normalized value.

If the visible phone format is important, a separate `phone_display` field could be used, but it is unnecessary for this assignment.

---

## 17. Admin Table Query

```sql
SELECT
    a.id,
    a.client_name,
    a.address,
    a.phone,
    a.car_license,
    a.car_engine,
    a.appointment_date,
    a.mechanic_id,
    m.name AS mechanic_name
FROM appointments a
INNER JOIN mechanics m
    ON m.id = a.mechanic_id
ORDER BY a.appointment_date ASC, a.id ASC;
```

Optional filters must use prepared parameters.

---

## 18. Responsive Architecture

### Desktop

```text
Hero: text + image + capacity
Main area: booking form + availability
Admin: sidebar + main table + edit panel
```

### Tablet

```text
Hero: two rows
Mechanics: two cards per row
Admin: compact sidebar or top menu
Table: horizontal scroll
```

### Mobile

```text
Single column
Hamburger navigation
Full-width form and buttons
Stacked mechanic cards
Summary below form
Admin table inside scroll container
Edit panel becomes full-width modal
```

---

## 19. Error Handling Architecture

### Validation error

- return exact field message;
- keep safe submitted values;
- do not write to database.

### Duplicate

- return a specific duplicate message;
- do not attempt insert.

### Full mechanic

- return a specific capacity message;
- refresh displayed availability.

### Database failure

- rollback transaction;
- log internal details;
- show generic user-safe message.

### Network failure during availability request

- keep mechanic selection disabled;
- show “Availability could not be loaded. Please try again.”

---

## 20. Deployment Architecture

### Local

```text
C:\xampp\htdocs\midnight-bento-garage\
```

- create database in phpMyAdmin;
- import SQL file;
- configure local credentials;
- open through `http://localhost/...`.

### Production

1. create production database;
2. import schema and mechanic seed data;
3. upload project files;
4. update database connection;
5. verify PHP version;
6. test public User Panel;
7. create real bookings;
8. test Admin Panel;
9. test date/mechanic updates;
10. confirm duplicate and capacity rejection;
11. remove test records if necessary.

---

## 21. Architecture Boundaries

Do not add:

- API framework;
- MVC framework;
- ORM;
- Node.js backend;
- React/Vue frontend;
- external authentication provider;
- payment gateway;
- background job system;
- unnecessary REST architecture.

The architecture should stay visibly connected to HTML, CSS, JavaScript, PHP, and MySQL.
