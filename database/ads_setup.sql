INSERT INTO settings (setting_key, setting_value) VALUES 
('enable_ads', '0'),
('admob_app_id', 'ca-app-pub-3940256099942544~3347511713'),
('banner_ad_id', 'ca-app-pub-3940256099942544/6300978111'),
('rewarded_ad_id', 'ca-app-pub-3940256099942544/5224354917'),
('interstitial_ad_id', 'ca-app-pub-3940256099942544/1033173712')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
