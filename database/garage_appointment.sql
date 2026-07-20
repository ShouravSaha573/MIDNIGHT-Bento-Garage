CREATE DATABASE IF NOT EXISTS midnight_bento_garage
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE midnight_bento_garage;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS mechanics;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    public_id CHAR(36) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(30) NOT NULL UNIQUE,
    phone VARCHAR(20) NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    admin_flag TINYINT(1) NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    must_change_password TINYINT(1) NOT NULL DEFAULT 0,
    failed_login_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until DATETIME NULL,
    last_login_at DATETIME NULL,
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_users_role_name (admin_flag, full_name),
    INDEX idx_users_public_name (public_id, full_name)
) ENGINE=InnoDB;

CREATE TABLE mechanics (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role_title VARCHAR(100) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE appointments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    client_name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    car_license VARCHAR(50) NOT NULL,
    car_engine VARCHAR(50) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NULL,
    status ENUM('scheduled', 'completed') NOT NULL DEFAULT 'scheduled',
    completed_at DATETIME NULL,
    mechanic_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_appointments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_appointments_mechanic FOREIGN KEY (mechanic_id) REFERENCES mechanics(id) ON DELETE RESTRICT,
    INDEX idx_appointment_customer_name (user_id, client_name),
    INDEX idx_appointment_status (status, appointment_date, appointment_time),
    INDEX idx_mechanic_date (mechanic_id, appointment_date),
    INDEX idx_appointment_date (appointment_date)
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    actor_user_id INT UNSIGNED NULL,
    action VARCHAR(80) NOT NULL,
    target_type VARCHAR(50) NOT NULL,
    target_id INT UNSIGNED NULL,
    details_json JSON NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_actor FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audit_actor_created (actor_user_id, created_at),
    INDEX idx_audit_action_created (action, created_at)
) ENGINE=InnoDB;

INSERT INTO users
(public_id, full_name, username, phone, password_hash, admin_flag, active, must_change_password)
VALUES
('MBG-7D3D243481F246A26E7A01AD79800359', 'Shourav Admin', 'admin_Shourav', NULL,
 '$2y$12$imu9sX4UrN/vLfJsDh1fQ.UG3FelDDoOsi6mKjzVoYXQPE45EzAE2', 1, 1, 1);

INSERT INTO mechanics (name, role_title) VALUES
('Alex Raj', 'Senior Mechanic'),
('Sam Michael', 'Auto Specialist'),
('John Davis', 'Lead Technician'),
('Mike Chen', 'Hybrid Specialist'),
('Ravi Sharma', 'Diagnostics Expert');
