-- Add banner settings to the database
-- Run this SQL to add banner configuration

INSERT INTO settings (setting_key, setting_value, description) VALUES
-- Whitelisted Countries Banner
('whitelist_banner_image', '', 'Banner image URL for whitelisted countries'),
('whitelist_banner_url', 'https://example.com', 'Banner click URL for whitelisted countries'),

-- Non-Whitelisted Countries Banner
('alt_banner_image', '', 'Banner image URL for non-whitelisted countries'),
('alt_banner_url', 'https://example.com', 'Banner click URL for non-whitelisted countries')

ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
