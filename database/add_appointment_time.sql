USE midnight_bento_garage;

ALTER TABLE appointments
    ADD COLUMN appointment_time TIME NULL AFTER appointment_date;
