# TravelATH - Travel Agency Management System

## Project Structure

The project has been reorganized following industry best practices with clear separation of concerns:

```
TravelATH/
├── config/                      # Configuration files
│   ├── db_connect.php          # Database connection configuration
│   └── mail_config.php         # Email/PHPMailer configuration
│
├── src/                         # Source code
│   ├── controllers/            # Business logic controllers (future)
│   ├── models/                 # Database models (future)
│   ├── services/               # Business services
│   │   └── send_hotel_email.php
│   └── middleware/             # Middleware (future)
│
├── public/                      # Public-facing files (web root)
│   ├── index.php               # Main dashboard
│   ├── Itinerary.php           # Itinerary management page
│   ├── login.html              # Login page
│   │
│   ├── api/                    # API endpoints
│   │   └── api.php             # Main API handler
│   │
│   └── assets/                 # Static assets
│       ├── css/                # Stylesheets
│       ├── js/                 # JavaScript files
│       └── images/             # Images
│
├── vendor/                      # Third-party libraries
│   └── PHPMailer/              # Email library
│
├── utils/                       # Utility files
│   ├── auth.php                # Authentication handler
│   ├── check_session.php       # Session validation
│   └── verfiy_setup.php        # Setup verification
│
├── scripts/                     # Setup/maintenance scripts
│   └── create_user.php         # User creation script
│
└── docs/                        # Documentation
    └── README.md               # This file
```

## Directory Descriptions

### `/config`
Contains all configuration files including database and email settings. These files should be secured and not exposed publicly.

### `/src`
Source code directory containing:
- **controllers/**: Future home for MVC controllers
- **models/**: Future home for database models
- **services/**: Business logic services (email, etc.)
- **middleware/**: Future home for middleware (auth, logging, etc.)

### `/public`
The web-accessible directory. This should be your web server's document root.
- **index.php**: Main application dashboard
- **Itinerary.php**: Itinerary management interface
- **login.html**: User login page
- **api/**: REST API endpoints
- **assets/**: Static files (CSS, JS, images)

### `/vendor`
Third-party libraries and dependencies (PHPMailer, etc.)

### `/utils`
Utility scripts for authentication, session management, and setup

### `/scripts`
Administrative and setup scripts (not web-accessible)

### `/docs`
Project documentation

## File Path Updates Required

After reorganization, the following path updates are needed in files:

### In `public/index.php`:
```php
// OLD: require_once 'check_session.php';
// NEW:
require_once '../utils/check_session.php';
```

### In `public/Itinerary.php`:
```php
// OLD: require_once 'check_session.php';
// NEW:
require_once '../utils/check_session.php';
```

### In `public/api/api.php`:
```php
// OLD: require_once 'check_session.php';
// OLD: include 'db_connect.php';
// NEW:
require_once '../../utils/check_session.php';
include '../../config/db_connect.php';
```

### In `utils/check_session.php`:
```php
// Update any paths if needed
```

### In `utils/auth.php`:
```php
// OLD: require_once 'db_connect.php';
// NEW:
require_once '../config/db_connect.php';
```

### In `src/services/send_hotel_email.php`:
```php
// OLD: require_once 'PHPMailer/src/PHPMailer.php';
// NEW:
require_once '../../vendor/PHPMailer/src/PHPMailer.php';
// And update other PHPMailer paths similarly
```

## Web Server Configuration

### Apache (.htaccess in root)
```apache
# Redirect all requests to public directory
RewriteEngine On
RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^(.*)$ /public/$1 [L]
```

### Or update your Apache VirtualHost:
```apache
<VirtualHost *:80>
    ServerName travelath.local
    DocumentRoot "c:/xampp/htdocs/TravelATH/public"
    
    <Directory "c:/xampp/htdocs/TravelATH/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## Benefits of This Structure

1. **Security**: Configuration and source files are outside the public directory
2. **Maintainability**: Clear separation of concerns makes code easier to maintain
3. **Scalability**: Easy to add new features following the established pattern
4. **Professional**: Follows industry-standard PHP project structure
5. **Version Control**: Better .gitignore organization (config/ can be excluded)

## Next Steps

1. Update all file paths in moved files
2. Test all functionality
3. Consider refactoring `api.php` into separate controller files
4. Add proper error handling and logging
5. Implement proper routing system

## Development

To run the application:
1. Ensure your web server points to the `public/` directory
2. Update database credentials in `config/db_connect.php`
3. Update email credentials in `config/mail_config.php`
4. Access the application at `http://localhost/TravelATH/public/` or your configured domain

## Support

For issues or questions, refer to the project documentation or contact the development team.