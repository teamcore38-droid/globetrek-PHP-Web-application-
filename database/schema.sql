-- GlobeTrek Adventures Database Schema & Initial Seed Data
-- Module: CSE5009 Web Application Development
-- Target Database: MySQL / MariaDB

CREATE DATABASE IF NOT EXISTS `globetrek_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `globetrek_db`;

-- 1. Roles Table
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `description` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Users Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT NOT NULL,
    `full_name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(30) NULL,
    `address` VARCHAR(255) NULL,
    `avatar` VARCHAR(255) DEFAULT 'default_avatar.png',
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 3. Tour Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `icon` VARCHAR(50) DEFAULT 'bi-compass',
    `image` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. Tour Packages Table
CREATE TABLE IF NOT EXISTS `packages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(220) NOT NULL UNIQUE,
    `destination` VARCHAR(150) NOT NULL,
    `duration_days` INT NOT NULL DEFAULT 1,
    `duration_nights` INT NOT NULL DEFAULT 0,
    `price` DECIMAL(10,2) NOT NULL,
    `discount_price` DECIMAL(10,2) NULL,
    `max_travelers` INT NOT NULL DEFAULT 15,
    `difficulty` ENUM('Easy', 'Moderate', 'Challenging') DEFAULT 'Easy',
    `featured` TINYINT(1) DEFAULT 0,
    `image` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `itinerary` TEXT NOT NULL, -- JSON or structured text
    `inclusions` TEXT NOT NULL, -- JSON or separated list
    `exclusions` TEXT NOT NULL,
    `accommodation_info` VARCHAR(255) NOT NULL,
    `transport_info` VARCHAR(255) NOT NULL,
    `status` ENUM('active', 'inactive', 'draft') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_packages_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 5. Bookings Table
CREATE TABLE IF NOT EXISTS `bookings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_ref` VARCHAR(30) NOT NULL UNIQUE,
    `user_id` INT NOT NULL,
    `package_id` INT NOT NULL,
    `travel_date` DATE NOT NULL,
    `travelers_count` INT NOT NULL DEFAULT 1,
    `subtotal` DECIMAL(10,2) NOT NULL,
    `discount` DECIMAL(10,2) DEFAULT 0.00,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `special_requests` TEXT NULL,
    `emergency_contact` VARCHAR(100) NULL,
    `booking_status` ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    `payment_status` ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
    `payment_method` VARCHAR(50) DEFAULT 'Card Payment',
    `staff_notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_bookings_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 6. Payments Table
CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_id` INT NOT NULL,
    `transaction_ref` VARCHAR(60) NOT NULL UNIQUE,
    `amount` DECIMAL(10,2) NOT NULL,
    `payment_method` VARCHAR(50) NOT NULL,
    `status` ENUM('successful', 'pending', 'failed') DEFAULT 'successful',
    `card_last4` VARCHAR(4) NULL,
    `paid_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 7. Custom Trip Inquiries Table (Customer Trip Planning)
CREATE TABLE IF NOT EXISTS `custom_trips` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `full_name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(30) NOT NULL,
    `destinations` VARCHAR(255) NOT NULL,
    `departure_date` DATE NOT NULL,
    `duration_days` INT NOT NULL DEFAULT 3,
    `adults_count` INT NOT NULL DEFAULT 2,
    `children_count` INT NOT NULL DEFAULT 0,
    `accommodation_type` ENUM('Budget Guesthouse', '3-Star Hotel', '4-Star Luxury', '5-Star Resort / Villa') DEFAULT '3-Star Hotel',
    `transport_type` ENUM('Private Car (Sedan)', 'Luxury Mini-Van', 'SUV / 4WD', 'Public Scenic Train + TukTuk') DEFAULT 'Private Car (Sedan)',
    `estimated_budget_usd` DECIMAL(10,2) NULL,
    `special_interests` TEXT NULL,
    `status` ENUM('pending', 'in_review', 'quoted', 'booked', 'closed') DEFAULT 'pending',
    `staff_response` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_custom_trips_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 8. General Customer Inquiries / Contact Messages Table
CREATE TABLE IF NOT EXISTS `inquiries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(30) NULL,
    `subject` VARCHAR(200) NOT NULL,
    `message` TEXT NOT NULL,
    `reply` TEXT NULL,
    `replied_by` INT NULL,
    `status` ENUM('pending', 'replied') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `replied_at` DATETIME NULL,
    CONSTRAINT `fk_inquiries_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_inquiries_replier` FOREIGN KEY (`replied_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 9. Reviews & Ratings Table
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `package_id` INT NOT NULL,
    `rating` INT NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `comment` TEXT NOT NULL,
    `status` ENUM('approved', 'pending', 'rejected') DEFAULT 'approved',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_reviews_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ==========================================
-- SEED DATA
-- ==========================================

-- Insert Roles
INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'admin', 'System Administrator with overall management and reporting privileges'),
(2, 'staff', 'Travel Agency Staff managing packages, bookings, and customer inquiries'),
(3, 'customer', 'Registered Travelers booking tours and customizing itineraries')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Insert Users (Default Passwords: Admin@123, Staff@123, Traveler@123)
INSERT INTO `users` (`id`, `role_id`, `full_name`, `email`, `password`, `phone`, `address`, `status`) VALUES
(1, 1, 'Chief Administrator', 'admin@globetrek.com', '$2y$10$PqSE5r0UNjcrGxLILjEOxejOBgxRJpulQK6dod7fspNlD.3Z1BMjK', '+94 31 223 8890', 'Main Street, Negombo, Sri Lanka', 'active'),
(2, 2, 'Kasun Perera (Travel Specialist)', 'staff@globetrek.com', '$2y$10$ar9ZH/ubX1gezJnuDoTn.eKnC/1kt8ZCeeuv9X/myd8IRDTfF9uFO', '+94 77 123 4567', 'Lewis Place, Negombo, Sri Lanka', 'active'),
(3, 3, 'Sarah Jenkins (Traveler)', 'traveler@gmail.com', '$2y$10$MP9Z2SAB7zcLhGYhh9HjpeV89bPM2XdeoBI9EjCJUG5rwiHKrHhSm', '+44 7911 123456', 'London, United Kingdom', 'active')
ON DUPLICATE KEY UPDATE `email`=VALUES(`email`);

-- Insert Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `image`) VALUES
(1, 'Coastal & Lagoon', 'coastal-lagoon', 'Relaxing sea side tours, catamaran lagoon rides, and golden sandy beaches.', 'bi-water', 'category_coastal.jpg'),
(2, 'Cultural Heritage', 'cultural-heritage', 'Ancient UNESCO World Heritage ruins, royal temples, and spiritual wonders.', 'bi-bank', 'category_heritage.jpg'),
(3, 'Hill Country & Nature', 'hill-country-nature', 'Misty mountain trails, lush green tea valleys, and iconic scenic train rides.', 'bi-tree', 'category_nature.jpg'),
(4, 'Wildlife & Safari', 'wildlife-safari', 'Thrilling jeep safaris encountering wild leopards, elephant herds, and blue whales.', 'bi-binoculars', 'category_wildlife.jpg'),
(5, 'Adventure & Hiking', 'adventure-hiking', 'Mountain trekking, rock climbing, white water rafting, and outdoor camping.', 'bi-compass', 'category_adventure.jpg')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Insert Tour Packages
INSERT INTO `packages` (`id`, `category_id`, `title`, `slug`, `destination`, `duration_days`, `duration_nights`, `price`, `discount_price`, `max_travelers`, `difficulty`, `featured`, `image`, `description`, `itinerary`, `inclusions`, `exclusions`, `accommodation_info`, `transport_info`, `status`) VALUES
(1, 1, 'Negombo Coastal Haven & Dutch Canal Safari', 'negombo-coastal-haven', 'Negombo, Western Province', 2, 1, 180.00, 150.00, 12, 'Easy', 1, 'package_negombo.jpg', 
'Explore the vibrant heart of Sri Lanka\'s little Rome! Experience traditional catamaran fishing boats at Negombo Lagoon, cruise through 17th-century Dutch canals, explore the bustling Fish Lellama, and unwind with fresh seafood alongside sunset beaches.',
'Day 1: Arrival & greeting at Negombo headquarters -> Morning private boat cruise through historic Dutch Canal and mangroves -> Seafood lunch at Lewis Place -> Afternoon visit to St. Mary\'s Cathedral and Dutch Fort ruins -> Sunset leisure at Negombo Beach.\r\nDay 2: Early morning traditional Catamaran ride with local fishermen -> Negombo fish market tour -> Authentic local culinary masterclass -> Departure.',
'Private AC transport throughout\r\nEnglish speaking certified local guide\r\n1 Night 4-Star beachfront resort stay with breakfast\r\nDutch canal boat cruise fees\r\nAll taxes and lagoon entry permits',
'International flight tickets\r\nPersonal expenses & alcoholic drinks\r\nOptional water sports (jet ski/windsurfing)',
'Heritance Negombo / Jetwing Blue (4-Star Beachfront Deluxe Room)',
'Air-conditioned private vehicle & local licensed boat skipper', 'active'),

(2, 2, 'Sigiriya Rock & Ancient Cultural Triangle', 'sigiriya-ancient-cultural-triangle', 'Sigiriya & Dambulla', 3, 2, 380.00, 340.00, 16, 'Moderate', 1, 'package_sigiriya.jpg',
'Journey into Sri Lanka\'s grand ancient past. Climb the monumental UNESCO World Heritage Sigiriya Lion Rock Fortress, wander the sacred cave monasteries of Dambulla, and experience an authentic bullock cart village safari with traditional Sri Lankan lunch.',
'Day 1: Scenic drive from Negombo to Dambulla -> Guided exploration of ancient Dambulla Golden Cave Temple -> Check-in at jungle eco-resort -> Evening traditional herbal Ayurvedic massage.\r\nDay 2: Early morning ascent of Sigiriya Lion Rock Fortress before sunrise heat -> Visit to ancient Royal Gardens & Mirror Wall -> Traditional Hiriwadunna village tour with canoe ride and rice & curry lunch -> Sunset view over Sigiriya from Pidurangala.\r\nDay 3: Morning excursion to ancient Polonnaruwa ruins -> Return transfer to Negombo or Bandaranaike International Airport.',
'All entrance tickets to Sigiriya Fortress & Dambulla Caves\r\n2 Nights 4-Star Eco-Resort accommodation with Half-Board (Breakfast & Dinner)\r\nVillage experience tour with traditional village lunch\r\nLicensed national tourist chauffeur guide\r\nPrivate luxury AC vehicle with toll charges',
'Tips for chauffeurs and site guides\r\nLunch meals outside package plan\r\nPersonal travel insurance',
'Aliya Resort & Spa / Cinnamon Lodge Habarana',
'Private Mercedes/Toyota HiAce luxury air-conditioned van', 'active'),

(3, 3, 'Ella Mountain Odyssey & Scenic Tea Rails', 'ella-mountain-odyssey', 'Ella & Nuwara Eliya', 4, 3, 490.00, 440.00, 10, 'Moderate', 1, 'package_ella.jpg',
'Experience the breathtaking mountain landscapes of Sri Lanka. Ride the world-renowned blue train through pine forests and misty viaducts, stand atop the Nine Arch Bridge, hike Little Adam\'s Peak, and tour colonial tea estates.',
'Day 1: Transfer from Negombo through cascading Devon & St. Clair waterfalls -> Nuwara Eliya "Little England" walking tour & colonial Gregory Lake stroll -> 4-Star boutique stay.\r\nDay 2: Board the iconic highland Blue Scenic Train from Nanu Oya to Ella -> Witness breathtaking gorge vistas -> Check into Ella mountain-view lodge -> Evening cafe walk.\r\nDay 3: Sunrise hike to Little Adam\'s Peak -> Walk across the historic Nine Arch railway bridge -> Afternoon swim at Ravana Falls -> Sunset cocktail overlooking Ella Rock.\r\nDay 4: Morning Ceylon Tea factory plucking experience -> Return scenic drive back to Negombo.',
'Reserved First/Second Class observation train tickets\r\n3 Nights boutique hill-country accommodation with daily breakfast & dinner\r\nFactory tour with premium Ceylon tea tasting\r\nPrivate AC transport with professional English-speaking guide\r\nBottled mineral water throughout tour',
'Lunch and personal bar bills\r\nExtreme adventure activities (Ella Mega Zip-line tickets optional)',
'98 Acres Resort / Ella Flower Garden Resort',
'Private Air-Conditioned SUV / Minivan & Highland Train', 'active'),

(4, 4, 'Yala Wild Leopard & Southern Ocean Whales', 'yala-wild-leopard-safari', 'Yala & Mirissa Coast', 4, 3, 560.00, 510.00, 8, 'Moderate', 1, 'package_yala.jpg',
'The ultimate wildlife thrill! Embark on deep wilderness 4x4 safaris inside Yala National Park—home to the world\'s highest leopard density—followed by an unforgettable ocean catamaran voyage spotting majestic blue whales in Mirissa and strolling Galle Dutch Fort.',
'Day 1: Departure towards southern wildlife territory -> Afternoon arrival at safari camp bordering Yala -> Evening campfire and BBQ dinner under stars.\r\nDay 2: Dawn 4x4 Jeep Safari into Block 1 Yala (tracking leopards, sloth bears, wild elephants) -> Afternoon rest by pool -> Sunset safari into tranquil bird sanctuaries.\r\nDay 3: Scenic coastal drive to Mirissa beach -> Evening leisure and beachfront candlelight dinner.\r\nDay 4: Dawn luxury catamaran cruise for Blue Whale watching in deep ocean -> Afternoon guided heritage tour inside Galle 16th-Century UNESCO Dutch Fort -> Return to Negombo.',
'Two exclusive 4x4 Open Safari Jeep sessions with wildlife tracker\r\nYala National Park permits and wildlife conservation fees\r\nMirissa Whale Watching Catamaran tickets with breakfast on board\r\n3 Nights luxury glamping & 4-star beach hotel\r\nPrivate transport throughout',
'Personal tips for safari tracker\r\nBeverages and alcoholic drinks',
'Cinnamon Wild Yala / Jetwing Yala & Mirissa Bay Hotel',
'Custom 4x4 Land Cruiser Safari Jeep + Luxury AC Passenger Van', 'active'),

(5, 5, 'Knuckles Peak Rainforest Trek & Wilderness Camp', 'knuckles-peak-rainforest-trek', 'Knuckles Mountain Range', 3, 2, 320.00, 290.00, 8, 'Challenging', 0, 'package_knuckles.jpg',
'For the avid trekker and outdoor enthusiast! Immerse yourself in the cloud forests of the Knuckles Conservation Range. Trek hidden river trails, plunge into crystal clear mountain pools, and camp under pristine starlit skies.',
'Day 1: Early departure from Negombo to Kandy foothills -> Basecamp briefing -> 10km trek through pygmy cloud forest -> Wild camping setup beside cascading stream.\r\nDay 2: Full day ridge hike across High Peaks (Corbett\'s Gap) -> Natural waterfall natural slides and swimming -> Traditional campfire barbecue with local musicians.\r\nDay 3: Morning bird-watching and biodiversity photography trek -> Descent to village -> Return transfer to Negombo.',
'Complete high-grade camping gear & sleeping bags\r\nCertified wilderness guide and emergency first aider\r\nAll meals prepared fresh at campsite (all inclusive)\r\nForest department entry permits\r\n4WD mountain transfer to trailheads',
'Personal hiking boots and backpack\r\nTravel insurance with adventure coverage',
'Eco-Wilderness Safari Glamping Tents with camp amenities',
'4x4 Off-Road Jeep from Kandy to base trail', 'active')
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- Insert Sample Bookings
INSERT INTO `bookings` (`id`, `booking_ref`, `user_id`, `package_id`, `travel_date`, `travelers_count`, `subtotal`, `discount`, `total_amount`, `special_requests`, `booking_status`, `payment_status`, `payment_method`, `staff_notes`) VALUES
(1, 'GTA-2026-001', 3, 1, '2026-11-15', 2, 300.00, 0.00, 300.00, 'Vegetarian meals preferred for Day 1 dinner.', 'confirmed', 'paid', 'Card Payment', 'Resort room booked facing ocean. Boat skipper assigned.'),
(2, 'GTA-2026-002', 3, 2, '2026-12-05', 2, 680.00, 40.00, 640.00, 'Airport pickup requested on morning of departure.', 'pending', 'paid', 'Card Payment', 'Awaiting flight arrival confirmation details.')
ON DUPLICATE KEY UPDATE `booking_ref`=VALUES(`booking_ref`);

-- Insert Sample Payments
INSERT INTO `payments` (`id`, `booking_id`, `transaction_ref`, `amount`, `payment_method`, `status`, `card_last4`, `paid_at`) VALUES
(1, 1, 'TXN-98421038', 300.00, 'Visa Debit', 'successful', '4242', '2026-10-06 14:20:00'),
(2, 2, 'TXN-98421039', 640.00, 'MasterCard', 'successful', '8812', '2026-10-06 16:45:00')
ON DUPLICATE KEY UPDATE `transaction_ref`=VALUES(`transaction_ref`);

-- Insert Sample Inquiries
INSERT INTO `inquiries` (`id`, `user_id`, `name`, `email`, `phone`, `subject`, `message`, `reply`, `replied_by`, `status`, `created_at`, `replied_at`) VALUES
(1, 3, 'Sarah Jenkins', 'traveler@gmail.com', '+44 7911 123456', 'Custom Family Tour for 5 members', 'Hello GlobeTrek team, we are planning a 7-day family tour across Sri Lanka including 2 children in January. Can you organize kid-friendly activities?', 'Dear Sarah, yes absolutely! We can easily tailor a relaxed itinerary with wildlife safaris, calm beach days in Mirissa, and scenic train travel. Our team will send you a quote shortly.', 2, 'replied', '2026-10-05 10:15:00', '2026-10-05 14:30:00'),
(2, NULL, 'David Miller', 'david.m@outlook.com', '+61 400 987654', 'Airport pickup from BIA to Negombo Hotel', 'Do you provide direct chauffeur pickup from Bandaranaike International Airport (CMB) to hotels in Lewis Place Negombo?', NULL, NULL, 'pending', '2026-10-06 17:00:00', NULL)
ON DUPLICATE KEY UPDATE `subject`=VALUES(`subject`);

-- Insert Sample Reviews
INSERT INTO `reviews` (`id`, `user_id`, `package_id`, `rating`, `comment`, `status`) VALUES
(1, 3, 1, 5, 'The Negombo coastal lagoon boat safari was magical! Super clean boat, knowledgeable guide and the fresh prawns dinner at sunset was unforgettable.', 'approved'),
(2, 3, 2, 5, 'Climbing Sigiriya rock was on our bucket list. GlobeTrek organized the timing perfectly so we beat the heat and crowds. Highly recommended!', 'approved')
ON DUPLICATE KEY UPDATE `comment`=VALUES(`comment`);
