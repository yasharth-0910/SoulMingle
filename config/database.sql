CREATE DATABASE soulmingle;

USE soulmingle;

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_admin BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (id)
);

CREATE TABLE profiles (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    age INT,
    gender ENUM('Male', 'Female', 'Other'),
    religion VARCHAR(50),
    education VARCHAR(100),
    interests TEXT,
    photo_url VARCHAR(255),
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE matches (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    match_user_id INT NOT NULL,
    status ENUM('Interested', 'Connected', 'Rejected') DEFAULT 'Interested',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (match_user_id) REFERENCES users(id)
);

CREATE TABLE search_filters (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    min_age INT,
    max_age INT,
    religion VARCHAR(50),
    education VARCHAR(100),
    location VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Add notifications table
CREATE TABLE notifications (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    related_id INT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Add index to matches table for faster lookups
ALTER TABLE matches ADD INDEX idx_user_matches (user_id, match_user_id, status); 

-- Add these tables to your existing database.sql

CREATE TABLE site_settings (
    id INT NOT NULL AUTO_INCREMENT,
    site_name VARCHAR(100) NOT NULL,
    site_description TEXT,
    maintenance_mode BOOLEAN DEFAULT FALSE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE TABLE email_settings (
    id INT NOT NULL AUTO_INCREMENT,
    smtp_host VARCHAR(100),
    smtp_port INT,
    smtp_username VARCHAR(100),
    smtp_password VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE TABLE security_settings (
    id INT NOT NULL AUTO_INCREMENT,
    min_password_length INT DEFAULT 6,
    max_login_attempts INT DEFAULT 5,
    enable_2fa BOOLEAN DEFAULT FALSE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE TABLE reports (
    id INT NOT NULL AUTO_INCREMENT,
    reporter_id INT NOT NULL,
    reported_user_id INT NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending', 'resolved', 'dismissed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (reporter_id) REFERENCES users(id),
    FOREIGN KEY (reported_user_id) REFERENCES users(id)
);

-- Add is_banned column to users table if not exists
ALTER TABLE users ADD COLUMN is_banned BOOLEAN DEFAULT FALSE;

-- Insert default settings
INSERT INTO site_settings (site_name, site_description, maintenance_mode) 
VALUES ('SoulMingle', 'Find your perfect match', FALSE);

INSERT INTO email_settings (smtp_host, smtp_port, smtp_username, smtp_password) 
VALUES ('smtp.example.com', 587, 'user@example.com', 'password');

INSERT INTO security_settings (min_password_length, max_login_attempts, enable_2fa) 
VALUES (8, 5, FALSE);

-- Update existing profiles to be approved
UPDATE profiles SET is_approved = TRUE;