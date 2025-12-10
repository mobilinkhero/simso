-- Add popup configuration settings to the settings table
-- Run this after importing the main schema.sql

INSERT INTO settings (setting_key, setting_value, description) VALUES
-- Whitelisted Countries Error Dialog
('whitelist_error_title', 'Record Not Found', 'Error title for whitelisted countries'),
('whitelist_error_msg', 'We could not find any details for this number.', 'Error message for whitelisted countries'),
('whitelist_action_text', 'Contact Support', 'Button text for whitelisted error dialog (optional)'),
('whitelist_action_url', 'https://api.cnic.pro/app/admin/', 'Button URL for whitelisted error dialog (optional)'),

-- Alternative (Non-Whitelisted) Countries Error Dialog
('alt_error_title', 'No Network Data', 'Error title for non-whitelisted countries'),
('alt_error_msg', 'Network information is currently unavailable for this number.', 'Error message for non-whitelisted countries'),

-- Geo-Targeting Configuration
('whitelisted_countries', 'PK', 'Comma-separated ISO country codes for whitelisted countries')

ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
