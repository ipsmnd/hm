-- Historical Monuments Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS historical_monuments;
USE historical_monuments2;

-- Create monuments table
CREATE TABLE monuments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    city VARCHAR(100),
    state VARCHAR(100),
    description TEXT,
    historical_significance TEXT,
    architecture TEXT,
    period VARCHAR(100),
    type VARCHAR(50),
    builder VARCHAR(255),
    built_year VARCHAR(50),
    unesco_site BOOLEAN DEFAULT FALSE,
    featured BOOLEAN DEFAULT FALSE,
    opening_hours VARCHAR(255),
    entry_fee VARCHAR(100),
    best_time VARCHAR(100),
    image_url VARCHAR(500),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    popularity_score INT DEFAULT 0,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create indexes for better performance
CREATE INDEX idx_monuments_state ON monuments(state);
CREATE INDEX idx_monuments_period ON monuments(period);
CREATE INDEX idx_monuments_type ON monuments(type);
CREATE INDEX idx_monuments_unesco ON monuments(unesco_site);
CREATE INDEX idx_monuments_featured ON monuments(featured);
CREATE INDEX idx_monuments_created_at ON monuments(created_at);

-- Create states table for reference
CREATE TABLE states (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    region VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create periods table for reference
CREATE TABLE periods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    start_year INT,
    end_year INT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create monument types table for reference
CREATE TABLE monument_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create users table for admin functionality
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor', 'viewer') DEFAULT 'viewer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample data for Indian states
INSERT INTO states (name, region) VALUES
('Andhra Pradesh', 'South'),
('Arunachal Pradesh', 'Northeast'),
('Assam', 'Northeast'),
('Bihar', 'East'),
('Chhattisgarh', 'Central'),
('Delhi', 'North'),
('Goa', 'West'),
('Gujarat', 'West'),
('Haryana', 'North'),
('Himachal Pradesh', 'North'),
('Jharkhand', 'East'),
('Karnataka', 'South'),
('Kerala', 'South'),
('Madhya Pradesh', 'Central'),
('Maharashtra', 'West'),
('Manipur', 'Northeast'),
('Meghalaya', 'Northeast'),
('Mizoram', 'Northeast'),
('Nagaland', 'Northeast'),
('Odisha', 'East'),
('Punjab', 'North'),
('Rajasthan', 'West'),
('Sikkim', 'Northeast'),
('Tamil Nadu', 'South'),
('Telangana', 'South'),
('Tripura', 'Northeast'),
('Uttar Pradesh', 'North'),
('Uttarakhand', 'North'),
('West Bengal', 'East');

-- Insert historical periods
INSERT INTO periods (name, start_year, end_year, description) VALUES
('Ancient Period', -3300, 550, 'Indus Valley Civilization to Gupta Empire'),
('Buddhist Period', -600, 1200, 'Buddhist architectural and artistic period'),
('Gupta Period', 320, 550, 'Golden age of Indian architecture and arts'),
('Medieval Period', 550, 1526, 'Post-Gupta to pre-Mughal period'),
('Mughal Period', 1526, 1857, 'Mughal Empire architectural style'),
('Rajput Period', 600, 1947, 'Rajput kingdoms and their architectural legacy'),
('Maratha Period', 1674, 1818, 'Maratha Empire architectural contributions'),
('Sikh Period', 1799, 1849, 'Sikh Empire architectural style'),
('Colonial Period', 1858, 1947, 'British colonial architecture in India'),
('Modern Period', 1947, NULL, 'Post-independence architectural developments');

-- Insert monument types
INSERT INTO monument_types (name, description) VALUES
('Temple', 'Religious structures for Hindu, Jain, Buddhist worship'),
('Fort', 'Defensive structures and fortifications'),
('Palace', 'Royal residences and administrative buildings'),
('Tomb', 'Burial monuments and mausoleums'),
('Mosque', 'Islamic religious structures'),
('Church', 'Christian religious buildings'),
('Monastery', 'Buddhist and Jain monastic complexes'),
('Cave', 'Rock-cut architectural monuments'),
('Stepwell', 'Water conservation and storage structures'),
('Gateway', 'Monumental entrance structures'),
('Minaret', 'Tower structures, often part of mosques'),
('Memorial', 'Commemorative monuments'),
('Observatory', 'Astronomical observation structures'),
('Garden', 'Landscaped garden complexes'),
('Bridge', 'Historic bridge structures');

-- Insert sample monument data
INSERT INTO monuments (
    name, city, state, description, historical_significance, architecture, 
    period, type, builder, built_year, unesco_site, featured,
    opening_hours, entry_fee, best_time, image_url, latitude, longitude
) VALUES
(
    'Taj Mahal',
    'Agra',
    'Uttar Pradesh',
    'The Taj Mahal is an ivory-white marble mausoleum on the right bank of the river Yamuna in Agra, Uttar Pradesh, India. It was commissioned in 1632 by the Mughal emperor Shah Jahan to house the tomb of his favourite wife, Mumtaz Mahal; it also houses the tomb of Shah Jahan himself.',
    'The Taj Mahal represents the finest architectural and artistic achievement through perfect harmony and excellent craftsmanship in a whole range of Indo-Islamic sepulchral architecture. It is a masterpiece of architectural style in conception, treatment and execution.',
    'The Taj Mahal incorporates and expands on design traditions of Indo-Islamic and earlier Mughal architecture. Specific inspiration came from successful Timurid and Mughal buildings.',
    'Mughal Period',
    'Tomb',
    'Shah Jahan',
    '1632-1653',
    TRUE,
    TRUE,
    '6:00 AM - 6:30 PM (Closed on Fridays)',
    '₹50 for Indians, $15 for foreigners',
    'October to March',
    'https://images.unsplash.com/photo-1564507592333-c60657eea523?w=800&h=600&fit=crop',
    27.1751,
    78.0421
),
(
    'Red Fort',
    'Delhi',
    'Delhi',
    'The Red Fort or Lal Qila is a historic fortified palace of the Mughal emperors that served as their main residence in Delhi, India. Built by the fifth Mughal Emperor, Shah Jahan as the palace of his fortified capital Shahjahanabad.',
    'The Red Fort represents the creative genius and high aesthetic sense of the Mughal architects. It stands as a powerful symbol of Mughal grandeur and power.',
    'The Red Fort is built of red sandstone and marble. The planning and design of the Red Fort represents a culmination of architectural development initiated in 1526 CE.',
    'Mughal Period',
    'Fort',
    'Shah Jahan',
    '1638-1648',
    TRUE,
    TRUE,
    '9:30 AM - 4:30 PM (Closed on Mondays)',
    '₹35 for Indians, ₹500 for foreigners',
    'October to March',
    'https://images.unsplash.com/photo-1587474260584-136574528ed5?w=800&h=600&fit=crop',
    28.6562,
    77.2410
),
(
    'Hawa Mahal',
    'Jaipur',
    'Rajasthan',
    'Hawa Mahal is a palace in the city of Jaipur, India. Built from red and pink sandstone, it is on the edge of the City Palace, Jaipur, and extends to the Zenana, or women''s chambers.',
    'The Hawa Mahal was built in 1799 by the Rajput ruler, Maharaja Sawai Pratap Singh, grandson of Maharaja Sawai Jai Singh, the founder of the city of Jaipur, India.',
    'The structure was built in the form of the crown of Krishna, the Hindu god. Its unique five-storey exterior is akin to a honeycomb with its 953 small windows called Jharokhas.',
    'Rajput Period',
    'Palace',
    'Maharaja Sawai Pratap Singh',
    '1799',
    FALSE,
    TRUE,
    '9:00 AM - 4:30 PM',
    '₹50 for Indians, ₹200 for foreigners',
    'October to March',
    'https://images.unsplash.com/photo-1599661046827-dacde6976549?w=800&h=600&fit=crop',
    26.9239,
    75.8267
),
(
    'Ajanta Caves',
    'Aurangabad',
    'Maharashtra',
    'The Ajanta Caves are approximately 30 rock-cut Buddhist cave monuments dating from the 2nd century BCE to about 480 CE in the Aurangabad district of Maharashtra state in India.',
    'The caves include paintings and rock-cut sculptures described as among the finest surviving examples of ancient Indian art, particularly expressive paintings that present emotions through gesture, pose and form.',
    'These caves were carved out of volcanic lava rock located in the Sahyadri hills. The caves showcase ancient Indian architecture and are divided into Chaityas and Viharas.',
    'Buddhist Period',
    'Cave',
    'Buddhist Monks',
    '2nd century BCE - 480 CE',
    TRUE,
    TRUE,
    '9:00 AM - 5:30 PM (Closed on Mondays)',
    '₹40 for Indians, ₹600 for foreigners',
    'November to March',
    'https://images.unsplash.com/photo-1582510003544-4d00b7f74220?w=800&h=600&fit=crop',
    20.5520,
    75.7033
),
(
    'Mysore Palace',
    'Mysore',
    'Karnataka',
    'The Mysore Palace, also known as Amba Vilas Palace, is a historical palace and a royal residence. It is located in Mysore, Karnataka. It is one of the most famous tourist attractions in India.',
    'The palace is a treasure house of exquisite carvings and works of art from all over the world. The wonderful gardens, the extraordinary Durbar Hall and the archaeological museum are worth seeing.',
    'The palace exhibits an Indo-Saracenic style of architecture, which is a combination of Hindu, Mughal, Rajput, and Gothic styles of architecture.',
    'Wodeyar Dynasty',
    'Palace',
    'Wodeyar Dynasty',
    '1912',
    FALSE,
    TRUE,
    '10:00 AM - 5:30 PM',
    '₹70 for Indians, ₹200 for foreigners',
    'October to February',
    'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&h=600&fit=crop',
    12.3052,
    76.6553
),
(
    'Konark Sun Temple',
    'Konark',
    'Odisha',
    'The Konark Sun Temple is a 13th-century CE sun temple at Konark about 35 kilometres northeast of Puri city on the coastline in Puri district, Odisha, India.',
    'The temple is attributed to king Narasimhadeva I of the Eastern Ganga dynasty about 1250 CE. It is one of the most renowned monuments of India and has been a UNESCO World Heritage Site since 1984.',
    'Konark Sun Temple is designed as a gigantic chariot of the Sun God, Surya, with 24 wheels, and is dragged by 7 horses. All these elements have spokes which function as sun dials.',
    'Medieval Period',
    'Temple',
    'King Narasimhadeva I',
    '1250 CE',
    TRUE,
    TRUE,
    '6:00 AM - 8:00 PM',
    '₹40 for Indians, ₹600 for foreigners',
    'September to March',
    'https://images.unsplash.com/photo-1582510003544-4d00b7f74220?w=800&h=600&fit=crop',
    19.8876,
    86.0947
);

-- Create default admin user (password: admin123)
INSERT INTO users (username, email, password_hash, role) VALUES
('admin', 'admin@historicalmonuments.in', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');