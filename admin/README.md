# Abhiverdhya Admin Panel Setup Guide

## Overview
This admin panel allows you to manage contact form submissions from your Abhiverdhya Industries website. It includes:
- Dashboard with statistics
- Contact form submission management
- Simple authentication system
- Clean, responsive interface

## Features
- **Dashboard**: View contact statistics, recent submissions, and charts
- **Contact Management**: View, filter, search, and manage contact submissions
- **Status Tracking**: Track submission status (New, Read, Replied, Archived)
- **Admin Notes**: Add internal notes to contact submissions
- **Form Source Tracking**: See which form/page the contact came from
- **Responsive Design**: Works on desktop and mobile devices

## Setup Instructions

### 1. Database Setup
1. Start XAMPP and ensure MySQL is running
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Import the database structure:
   - Navigate to the `admin/database/` folder
   - Run the SQL commands in `setup.sql` to create the database and tables
   
   OR manually run these SQL commands:
   ```sql
   CREATE DATABASE IF NOT EXISTS abhiverdhya_admin;
   USE abhiverdhya_admin;
   
   -- Copy and paste the entire content from setup.sql
   ```

### 2. Default Admin Credentials
- **Username**: admin
- **Password**: admin123

⚠️ **Important**: Change the default password after first login!

### 3. Admin Panel Access
- **Admin Login**: http://localhost/Abhiverdhya/admin/login.php
- **Dashboard**: http://localhost/Abhiverdhya/admin/dashboard.php
- **Contact Management**: http://localhost/Abhiverdhya/admin/contacts.php

### 4. Website Integration
The admin panel is now integrated into your website:
- Admin link appears in the main navigation (red color)
- All contact forms automatically send data to the admin panel
- Form submissions are tracked by source (contact page, about page, popup form)

## Usage Instructions

### For Website Visitors
1. Fill out any contact form on the website
2. Submissions are automatically saved to the admin panel
3. Visitors receive confirmation messages upon successful submission

### For Administrators
1. Navigate to http://localhost/Abhiverdhya/admin/login.php
2. Login with admin credentials
3. View dashboard for statistics and recent activity
4. Manage contacts through the Contacts page:
   - View all submissions
   - Filter by status, date, or search terms
   - Click "View" to see full contact details
   - Update status (New → Read → Replied → Archived)
   - Add admin notes
   - Delete unwanted submissions

## Contact Form Sources
The system tracks submissions from:
- **contact_page**: Main contact page form
- **about_page**: About page contact form  
- **popup_form**: Homepage popup contact form

## Status Workflow
1. **New**: Fresh submissions (yellow badge)
2. **Read**: Viewed by admin (blue badge)
3. **Replied**: Admin has responded (green badge)
4. **Archived**: Closed/completed (gray badge)

## File Structure
```
admin/
├── assets/
│   └── admin-style.css          # Admin panel styling
├── config/
│   └── database.php             # Database configuration
├── database/
│   └── setup.sql               # Database setup script
├── handlers/
│   ├── auth_handler.php        # Authentication logic
│   └── contact_handler.php     # Form submission handler
├── contacts.php                # Contact management page
├── dashboard.php               # Admin dashboard
├── login.php                   # Login page
└── README.md                   # This file

js/
└── form-handler.js             # JavaScript for form submissions
```

## Security Notes
- Session-based authentication
- Password hashing using PHP's password_hash()
- Input validation and sanitization
- SQL injection protection using prepared statements

## Customization
- Change admin credentials in the database
- Modify email/notification settings in contact_handler.php
- Update styling in assets/admin-style.css
- Add more admin users through phpMyAdmin

## Troubleshooting

### Database Connection Issues
- Verify XAMPP MySQL is running
- Check database credentials in `config/database.php`
- Ensure database 'abhiverdhya_admin' exists

### Login Issues
- Verify default credentials (admin/admin123)
- Clear browser cache and cookies
- Check if sessions are working in PHP

### Form Submission Issues
- Check if form-handler.js is loaded on pages
- Verify contact_handler.php path is correct
- Check browser console for JavaScript errors
- Ensure database tables exist

### Permission Issues
- Ensure XAMPP has proper file permissions
- Check if PHP has write access to session files

## Support
For technical support or questions about the admin panel, contact your web developer or refer to the PHP/MySQL documentation.

---

**Version**: 1.0  
**Created**: August 2025  
**Last Updated**: August 2025
