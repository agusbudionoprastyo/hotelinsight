-- Sample Data untuk Hotel Insight
-- Jalankan file ini jika seeder gagal

-- 1. Insert OTA Sources
INSERT INTO ota_sources (name, slug, website_url, is_active, created_at, updated_at) VALUES
('Booking.com', 'booking-com', 'https://www.booking.com', 1, NOW(), NOW()),
('Agoda', 'agoda', 'https://www.agoda.com', 1, NOW(), NOW()),
('Expedia', 'expedia', 'https://www.expedia.com', 1, NOW(), NOW()),
('Hotels.com', 'hotels-com', 'https://www.hotels.com', 1, NOW(), NOW()),
('TripAdvisor', 'tripadvisor', 'https://www.tripadvisor.com', 1, NOW(), NOW()),
('Airbnb', 'airbnb', 'https://www.airbnb.com', 1, NOW(), NOW()),
('Traveloka', 'traveloka', 'https://www.traveloka.com', 1, NOW(), NOW()),
('Pegipegi', 'pegipegi', 'https://www.pegipegi.com', 1, NOW(), NOW());

-- 2. Insert Hotels
INSERT INTO hotels (name, description, address, city, country, phone, email, website, star_rating, image_url, is_active, created_at, updated_at) VALUES
('Grand Hotel Jakarta', 'Hotel bintang 5 dengan fasilitas mewah di pusat Jakarta', 'Jl. Sudirman No. 123', 'Jakarta', 'Indonesia', '+62-21-1234-5678', 'info@grandhoteljakarta.com', 'https://grandhoteljakarta.com', 5, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800', 1, NOW(), NOW()),
('Bali Paradise Resort', 'Resort eksklusif dengan pemandangan pantai di Bali', 'Jl. Pantai Kuta No. 45', 'Bali', 'Indonesia', '+62-361-9876-5432', 'reservations@baliparadise.com', 'https://baliparadise.com', 4, 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800', 1, NOW(), NOW()),
('Surabaya Business Hotel', 'Hotel bisnis modern di jantung Surabaya', 'Jl. Tunjungan No. 67', 'Surabaya', 'Indonesia', '+62-31-4567-8901', 'booking@surabayabusiness.com', 'https://surabayabusiness.com', 4, 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800', 1, NOW(), NOW());

-- 3. Insert Hotel Prices
INSERT INTO hotel_prices (hotel_id, ota_source_id, price, currency, check_in_date, check_out_date, room_type, booking_url, is_available, last_updated, created_at, updated_at) VALUES
(1, 1, 1500000, 'IDR', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Deluxe Room', 'https://www.booking.com', 1, NOW(), NOW(), NOW()),
(1, 2, 1450000, 'IDR', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Deluxe Room', 'https://www.agoda.com', 1, NOW(), NOW(), NOW()),
(1, 3, 1550000, 'IDR', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Deluxe Room', 'https://www.expedia.com', 1, NOW(), NOW(), NOW()),
(1, 4, 1480000, 'IDR', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Deluxe Room', 'https://www.hotels.com', 1, NOW(), NOW(), NOW()),
(2, 1, 1200000, 'IDR', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Deluxe Room', 'https://www.booking.com', 1, NOW(), NOW(), NOW()),
(2, 2, 1180000, 'IDR', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Deluxe Room', 'https://www.agoda.com', 1, NOW(), NOW(), NOW()),
(2, 3, 1250000, 'IDR', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Deluxe Room', 'https://www.expedia.com', 1, NOW(), NOW(), NOW()),
(2, 4, 1220000, 'IDR', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Deluxe Room', 'https://www.hotels.com', 1, NOW(), NOW(), NOW()),
(3, 1, 900000, 'IDR', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Deluxe Room', 'https://www.booking.com', 1, NOW(), NOW(), NOW()),
(3, 2, 880000, 'IDR', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Deluxe Room', 'https://www.agoda.com', 1, NOW(), NOW(), NOW()),
(3, 3, 920000, 'IDR', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Deluxe Room', 'https://www.expedia.com', 1, NOW(), NOW(), NOW()),
(3, 4, 890000, 'IDR', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Deluxe Room', 'https://www.hotels.com', 1, NOW(), NOW(), NOW());

-- 4. Insert Hotel Reviews
INSERT INTO hotel_reviews (hotel_id, ota_source_id, reviewer_name, rating, review_text, review_date, is_verified, created_at, updated_at) VALUES
(1, 1, 'Ahmad Rahman', 5, 'Hotel yang sangat nyaman dan pelayanan excellent!', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 1, NOW(), NOW()),
(1, 2, 'Sarah Johnson', 4, 'Fasilitas bagus, lokasi strategis, recommended!', DATE_SUB(CURDATE(), INTERVAL 10 DAY), 1, NOW(), NOW()),
(1, 3, 'Budi Santoso', 5, 'Staff ramah, kamar bersih, harga worth it.', DATE_SUB(CURDATE(), INTERVAL 15 DAY), 1, NOW(), NOW()),
(2, 1, 'Maria Garcia', 4, 'Resort yang indah dengan pemandangan pantai yang memukau.', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 1, NOW(), NOW()),
(2, 2, 'John Smith', 5, 'Pengalaman menginap yang luar biasa, akan kembali lagi!', DATE_SUB(CURDATE(), INTERVAL 8 DAY), 1, NOW(), NOW()),
(2, 3, 'Lisa Wong', 4, 'Fasilitas lengkap, staff sangat membantu.', DATE_SUB(CURDATE(), INTERVAL 12 DAY), 1, NOW(), NOW()),
(3, 1, 'David Lee', 4, 'Hotel bisnis yang nyaman untuk perjalanan kerja.', DATE_SUB(CURDATE(), INTERVAL 6 DAY), 1, NOW(), NOW()),
(3, 2, 'Anna Chen', 5, 'Lokasi strategis, dekat dengan pusat bisnis.', DATE_SUB(CURDATE(), INTERVAL 9 DAY), 1, NOW(), NOW()),
(3, 3, 'Robert Brown', 4, 'Pelayanan cepat dan efisien, recommended untuk bisnis.', DATE_SUB(CURDATE(), INTERVAL 14 DAY), 1, NOW(), NOW());

-- 5. Verifikasi Data
SELECT 'OTA Sources' as table_name, COUNT(*) as total FROM ota_sources
UNION ALL
SELECT 'Hotels' as table_name, COUNT(*) as total FROM hotels
UNION ALL
SELECT 'Hotel Prices' as table_name, COUNT(*) as total FROM hotel_prices
UNION ALL
SELECT 'Hotel Reviews' as table_name, COUNT(*) as total FROM hotel_reviews;
