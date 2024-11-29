-- Insert sample users (passwords are 'password123' hashed with bcrypt)
INSERT INTO users (username, email, password_hash, created_at) VALUES
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW()),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW()),
('mike_wilson', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW()),
('sarah_jones', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW()),
('david_brown', 'david@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW()),
('lisa_taylor', 'lisa@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW()),
('james_wilson', 'james@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW()),
('emma_davis', 'emma@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW());

-- Insert sample profiles
INSERT INTO profiles (user_id, age, gender, religion, education, interests, photo_url) VALUES
(1, 28, 'Male', 'Christian', 'Bachelor in Computer Science', 'Programming, hiking, photography', 'default-male.jpg'),
(2, 25, 'Female', 'Buddhist', 'Master in Business', 'Yoga, traveling, cooking', 'default-female.jpg'),
(3, 32, 'Male', 'Hindu', 'PhD in Engineering', 'Reading, music, sports', 'default-male.jpg'),
(4, 27, 'Female', 'Muslim', 'Bachelor in Psychology', 'Art, dancing, meditation', 'default-female.jpg'),
(5, 30, 'Male', 'Christian', 'Master in Architecture', 'Design, swimming, cycling', 'default-male.jpg'),
(6, 29, 'Female', 'Jewish', 'Bachelor in Education', 'Teaching, painting, gardening', 'default-female.jpg'),
(7, 31, 'Male', 'Atheist', 'Master in Physics', 'Science, gaming, movies', 'default-male.jpg'),
(8, 26, 'Female', 'Catholic', 'Bachelor in Arts', 'Writing, photography, travel', 'default-female.jpg');

-- Insert some matches (some interested, some connected)
INSERT INTO matches (user_id, match_user_id, status, created_at) VALUES
(1, 2, 'Interested', NOW()),
(3, 4, 'Connected', NOW()),
(4, 3, 'Connected', NOW()),
(5, 6, 'Interested', NOW()),
(7, 8, 'Connected', NOW()),
(8, 7, 'Connected', NOW()),
(1, 4, 'Interested', NOW()),
(2, 3, 'Interested', NOW()),
(5, 8, 'Interested', NOW()),
(6, 7, 'Interested', NOW());

-- Insert some notifications
INSERT INTO notifications (user_id, type, message, related_id, is_read, created_at) VALUES
(2, 'interest', 'John Doe has expressed interest in your profile!', 1, false, NOW()),
(4, 'match', 'You have a new match with Mike Wilson!', 3, false, NOW()),
(3, 'match', 'You have a new match with Sarah Jones!', 4, false, NOW()),
(6, 'interest', 'David Brown has expressed interest in your profile!', 5, false, NOW()),
(8, 'match', 'You have a new match with James Wilson!', 7, false, NOW()),
(7, 'match', 'You have a new match with Emma Davis!', 8, false, NOW());

-- Insert search filters for some users
INSERT INTO search_filters (user_id, min_age, max_age, religion, education) VALUES
(1, 23, 30, 'Christian', 'Bachelor'),
(2, 25, 35, NULL, 'Master'),
(3, 25, 30, 'Hindu', NULL),
(4, 28, 40, NULL, NULL); 