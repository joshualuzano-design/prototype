SET sql_mode='';

CREATE DATABASE IF NOT EXISTS intelligent_id_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE intelligent_id_db;

CREATE TABLE IF NOT EXISTS Admin (
  user_id VARCHAR(32) PRIMARY KEY,
  username VARCHAR(128) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS Student (
  student_id VARCHAR(32) PRIMARY KEY,
  full_name VARCHAR(255) NOT NULL,
  course VARCHAR(128),
  year_level INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ParentsContacts (
  contact_id VARCHAR(32) PRIMARY KEY,
  student_id VARCHAR(32) NOT NULL,
  contact_number VARCHAR(64),
  FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS AuthenticationLog (
  log_id VARCHAR(32) PRIMARY KEY,
  student_id VARCHAR(32),
  scan_date_time DATETIME,
  verification_status VARCHAR(128),
  FOREIGN KEY (student_id) REFERENCES Student(student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS SystemUsers (
  user_id VARCHAR(32) PRIMARY KEY,
  username VARCHAR(128) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ViolationRecords (
  record_id VARCHAR(32) PRIMARY KEY,
  student_id VARCHAR(32) NOT NULL,
  violation_type VARCHAR(255),
  consequences TEXT,
  handled_by VARCHAR(32),
  FOREIGN KEY (student_id) REFERENCES Student(student_id),
  FOREIGN KEY (handled_by) REFERENCES SystemUsers(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS AuditLog (
  log_id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id VARCHAR(32) NOT NULL,
  action VARCHAR(255) NOT NULL,
  details TEXT,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (admin_id) REFERENCES Admin(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS Vehicle (
  vehicle_id VARCHAR(32) PRIMARY KEY,
  student_id VARCHAR(32) NOT NULL,
  vehicle_type VARCHAR(128) NOT NULL,
  plate_number VARCHAR(32) NOT NULL,
  color VARCHAR(64),
  date_registered DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS Visitor (
  visitor_id VARCHAR(32) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  purpose VARCHAR(255),
  contact_number VARCHAR(64),
  date_visit DATETIME DEFAULT CURRENT_TIMESTAMP,
  student_id VARCHAR(32),
  FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS VehicleScanLog (
  scan_id VARCHAR(32) PRIMARY KEY,
  vehicle_id VARCHAR(32) NOT NULL,
  student_id VARCHAR(32),
  scan_date_time DATETIME DEFAULT CURRENT_TIMESTAMP,
  verification_status VARCHAR(128),
  is_suspicious BOOLEAN DEFAULT FALSE,
  duplicate_count INT DEFAULT 0,
  FOREIGN KEY (vehicle_id) REFERENCES Vehicle(vehicle_id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS StudentAttendance (
  attendance_id VARCHAR(32) PRIMARY KEY,
  student_id VARCHAR(32) NOT NULL,
  entry_time DATETIME DEFAULT CURRENT_TIMESTAMP,
  exit_time DATETIME,
  entry_status VARCHAR(128),
  parent_sms_sent BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS SuspiciousActivity (
  activity_id VARCHAR(32) PRIMARY KEY,
  activity_type VARCHAR(128),
  description TEXT,
  vehicle_id VARCHAR(32),
  student_id VARCHAR(32),
  detected_time DATETIME DEFAULT CURRENT_TIMESTAMP,
  severity VARCHAR(64),
  notified_admin BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (vehicle_id) REFERENCES Vehicle(vehicle_id) ON DELETE SET NULL,
  FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS EmergencyAlert (
  alert_id VARCHAR(32) PRIMARY KEY,
  disaster_type VARCHAR(128),
  description TEXT,
  severity VARCHAR(64),
  alert_time DATETIME DEFAULT CURRENT_TIMESTAMP,
  notified_admin BOOLEAN DEFAULT FALSE,
  notified_parents BOOLEAN DEFAULT FALSE,
  resolved BOOLEAN DEFAULT FALSE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS VisitorCheckIn (
  checkin_id VARCHAR(32) PRIMARY KEY,
  visitor_id VARCHAR(32) NOT NULL,
  visitor_name VARCHAR(255),
  visitor_phone VARCHAR(64),
  purpose VARCHAR(255),
  student_id VARCHAR(32),
  check_in_time DATETIME DEFAULT CURRENT_TIMESTAMP,
  check_out_time DATETIME,
  status VARCHAR(64),
  FOREIGN KEY (visitor_id) REFERENCES Visitor(visitor_id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO Admin (user_id, username, password, role) VALUES ('U001', 'admin', 'admin123', 'Administrator');
INSERT IGNORE INTO SystemUsers (user_id, username, password, role) VALUES ('U002', 'security01', 'securepass', 'Security');