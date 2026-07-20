USE midnight_bento_garage;

ALTER TABLE appointments
    DROP INDEX uq_one_appointment_per_customer,
    DROP INDEX uq_phone_date,
    ADD INDEX idx_appointment_customer_status (user_id, appointment_date, appointment_time);
