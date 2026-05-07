CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(15) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  otp_code VARCHAR(6) NULL,
  otp_expiry DATETIME NULL,
  is_verified TINYINT(1) DEFAULT 0,
  reset_token VARCHAR(64) NULL,
  reset_expiry DATETIME NULL,
  role ENUM('passenger','operator','admin') NOT NULL DEFAULT 'passenger',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS buses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bus_name VARCHAR(150) NOT NULL,
  total_seats INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS schedules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bus_id INT NOT NULL,
  source VARCHAR(100) NOT NULL,
  destination VARCHAR(100) NOT NULL,
  departure_time DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (bus_id) REFERENCES buses(id) ON DELETE CASCADE
);

INSERT INTO buses (bus_name, total_seats) VALUES
  ('Karachi Express', 42),
  ('Lahore Flyer', 36),
  ('Islamabad Premium', 30),
  ('Kharian Shuttle', 28);

INSERT INTO schedules (bus_id, source, destination, departure_time) VALUES
  (1, 'Karachi', 'Lahore', '2026-05-01 08:00:00'),
  (2, 'Lahore', 'Karachi', '2026-05-01 14:00:00'),
  (3, 'Lahore', 'Islamabad', '2026-05-02 09:00:00'),
  (3, 'Islamabad', 'Lahore', '2026-05-02 17:00:00'),
  (4, 'Lahore', 'Kharian', '2026-05-01 12:30:00'),
  (4, 'Kharian', 'Lahore', '2026-05-01 18:00:00');

CREATE TABLE IF NOT EXISTS tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  schedule_id INT NOT NULL,
  seats INT NOT NULL,
  status ENUM('pending','approved','boarded','cancelled') NOT NULL DEFAULT 'pending',
  boarded_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE
);
