-- Run once against ektamultp_astro_hari after taking a backup.
ALTER DATABASE `ektamultp_astro_hari` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE admin_users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE appointments CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE articles CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE contact_messages CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE panchang CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE payments CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE pooja_bookings CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE pooja_services CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE rewards CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE appointments ADD COLUMN IF NOT EXISTS meeting_url VARCHAR(500) NULL AFTER consultation_mode;

CREATE TABLE IF NOT EXISTS push_subscriptions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  endpoint VARCHAR(700) NOT NULL,
  p256dh VARCHAR(255) NOT NULL,
  auth VARCHAR(255) NOT NULL,
  language ENUM('ne','en') DEFAULT 'ne',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_push_endpoint (endpoint(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Prevent duplicate payment references and duplicate user accounts.
ALTER TABLE payments ADD UNIQUE KEY uq_payments_method_reference (method, transaction_ref);
ALTER TABLE users ADD UNIQUE KEY uq_users_phone (phone);

-- Ensure deleting a service/admin cannot orphan operational history.
ALTER TABLE pooja_bookings DROP FOREIGN KEY pooja_bookings_ibfk_1,
  ADD CONSTRAINT fk_pooja_bookings_service FOREIGN KEY (service_id) REFERENCES pooja_services(id) ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE rewards DROP FOREIGN KEY rewards_ibfk_1,
  ADD CONSTRAINT fk_rewards_admin FOREIGN KEY (awarded_by) REFERENCES admin_users(id) ON UPDATE CASCADE ON DELETE SET NULL;
