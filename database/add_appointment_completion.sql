USE midnight_bento_garage;

ALTER TABLE appointments
    ADD COLUMN status ENUM('scheduled', 'completed') NOT NULL DEFAULT 'scheduled' AFTER appointment_time,
    ADD COLUMN completed_at DATETIME NULL AFTER status,
    ADD INDEX idx_appointment_status (status, appointment_date, appointment_time);
