-- Database setup for Abhiverdhya Admin Panel
-- Create database
CREATE DATABASE IF NOT EXISTS abhiverdhya_admin;
USE abhiverdhya_admin;

-- Create admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Create contact submissions table
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    product_type VARCHAR(50) DEFAULT NULL,
    message TEXT NOT NULL,
    form_source VARCHAR(50) NOT NULL,
    status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    admin_notes TEXT DEFAULT NULL
);

-- Insert default admin user (username: admin, password: admin123)
-- Password is hashed using PHP password_hash()
INSERT INTO admin_users (username, password, email) VALUES 
('admin', 'admin123', 'admin@abhiverdhya.com');

-- Create indexes for better performance
CREATE INDEX idx_form_source ON contact_submissions(form_source);
CREATE INDEX idx_status ON contact_submissions(status);
CREATE INDEX idx_submitted_at ON contact_submissions(submitted_at);

-- Sample data for testing (optional)
INSERT INTO contact_submissions (name, email, phone, product_type, message, form_source, status) VALUES
('John Doe', 'john@example.com', '+91 9876543210', 'packaging', 'Interested in packaging solutions for food industry', 'contact_page', 'new'),
('Jane Smith', 'jane@example.com', '+91 9876543211', 'industrial', 'Need industrial components for automotive', 'popup_form', 'read'),
('Mike Johnson', 'mike@example.com', '+91 9876543212', 'medical', 'Medical grade plastics required', 'about_page', 'new');
