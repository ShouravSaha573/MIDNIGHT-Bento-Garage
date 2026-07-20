# Authentication and Security Upgrade

This addendum supersedes earlier statements that login was outside the project scope.

## Authentication structure

- `customer/register.php` creates customers only.
- `customer/login.php` accepts accounts where `admin_flag = false`.
- `admin/login.php` accepts accounts where `admin_flag = true`.
- `account/change_password.php` handles password changes and forced first-login changes.
- `logout.php` accepts POST requests with a CSRF token.
- `admin/create_admin.php` is the only application page that creates an admin.

## User tracking

- internal key: `users.id`;
- public random key: `users.public_id`;
- display/sort key: `full_name` plus `public_id`;
- appointment relationship: `appointments.user_id`.

## One-appointment rule

The upgraded database contains `UNIQUE(user_id)` in `appointments`, so one customer account can hold one appointment.

## Role behavior

Every protected request reloads the user row. Therefore changing `admin_flag` in the database changes the user's effective role on the next request.

## Security model

- hashed passwords;
- session regeneration and expiry;
- secure cookie settings;
- CSRF validation;
- prepared statements;
- output escaping;
- login lockout;
- reauthentication for admin creation;
- role-based server authorization;
- audit logs;
- security headers;
- production HTTPS requirement.
