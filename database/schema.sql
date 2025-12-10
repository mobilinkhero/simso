-- SIMSO SIM INFO Database Schema
-- Created for dynamic SIM information management

-- Create database
CREATE DATABASE IF NOT EXISTS simso_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE simso_db;

-- Table: sim_data
-- Stores SIM card information with dynamic fields
CREATE TABLE IF NOT EXISTS sim_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone_number VARCHAR(20) NOT NULL UNIQUE,
    operator_name VARCHAR(100) DEFAULT NULL,
    status VARCHAR(50) DEFAULT 'Active',
    data_plan VARCHAR(100) DEFAULT NULL,
    roaming VARCHAR(20) DEFAULT 'Off',
    registration_date DATE DEFAULT NULL,
    network_type VARCHAR(20) DEFAULT NULL,
    country VARCHAR(100) DEFAULT NULL,
    region VARCHAR(100) DEFAULT NULL,
    custom_fields JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_phone (phone_number),
    INDEX idx_operator (operator_name),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: api_logs
-- Tracks all API requests for analytics
CREATE TABLE IF NOT EXISTS api_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone_number VARCHAR(20) NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    response_status VARCHAR(20) DEFAULT 'success',
    response_time_ms INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_phone_log (phone_number),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: settings
-- App-wide settings and configurations
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT DEFAULT NULL,
    description TEXT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('app_name', 'SIMSO SIM INFO', 'Application name'),
('api_enabled', '1', 'Enable/disable API access'),
('default_operator', 'Unknown', 'Default operator name'),
('default_status', 'Active', 'Default SIM status'),
('max_requests_per_day', '1000', 'Maximum API requests per day'),
('maintenance_mode', '0', 'Enable maintenance mode')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Sample data for testing
INSERT INTO sim_data (phone_number, operator_name, status, data_plan, roaming, registration_date, network_type, country, region) VALUES
('+923001234567', 'Jazz', 'Active', '50GB Monthly', 'Off', '2023-01-15', '4G LTE', 'Pakistan', 'Punjab'),
('+923111234567', 'Telenor', 'Active', '100GB Monthly', 'On', '2023-03-20', '5G', 'Pakistan', 'Sindh'),
('+923211234567', 'Zong', 'Inactive', '25GB Weekly', 'Off', '2023-06-10', '4G', 'Pakistan', 'KPK'),
('+923331234567', 'Ufone', 'Active', 'Unlimited', 'Off', '2023-08-05', '4G LTE', 'Pakistan', 'Balochistan')
ON DUPLICATE KEY UPDATE operator_name = VALUES(operator_name);
