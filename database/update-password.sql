-- Update admin password to 'admin123'
UPDATE admin_users
SET password = '$2y$12$TaxCuQfyaw73ErWzRDyLCu0Sj0PHo3QaILoSsDvPTVmMFRfnf5nZK'
WHERE username = 'admin';
