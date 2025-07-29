# NexaCore-POS

NexaCore-POS is a comprehensive Point of Sale (POS) system built with Laravel, designed to streamline various business operations. This application provides a robust backend for managing sales, purchases, inventory, accounts, and more.

## Key Features

Based on the application's structure, NexaCore-POS is designed to handle the following core functionalities:

### Sales Management
*   **Order Processing**: Efficiently create and manage customer orders.
*   **Product Sales**: Handle the sale of various items, including pricing and quantity management.
*   **Tax Calculation**: Apply and manage taxes on sales transactions.

### Purchase Management
*   **Supplier Management**: Keep track of suppliers and their details.
*   **Purchase Orders**: Create and manage purchase orders for inventory replenishment.

### Inventory Management
*   **Item Management**: Define and manage various items, including their units and categories.
*   **Stock Control**: Monitor and update stock levels for all items.

### Financial Management
*   **Account Management**: Manage different financial accounts within the system.
*   **Expense Tracking**: Record and categorize business expenses.
*   **Payment Processing**: Handle various payment methods for sales and purchases.

### User and Role Management
*   **User Accounts**: Create and manage user accounts with different access levels.
*   **Role-Based Access Control**: Assign roles to users to control their permissions within the system.

### Customer and Party Management
*   **Customer Database**: Maintain a database of customer information.
*   **Party Management**: Manage other relevant parties involved in business operations.

### Reporting and Analytics
*   The system is structured to support various reports related to sales, purchases, expenses, and inventory, providing insights into business performance.

### Multi-language Support
*   The application supports multiple languages (Arabic, English, Hindi), allowing for a localized user experience.

## Technology Stack

*   **Backend**: Laravel (PHP Framework)
*   **Frontend**: JavaScript, CSS (likely with Tailwind CSS based on `tailwind.config.js`)
*   **Build Tool**: Vite
*   **Dependency Management**: Composer (PHP), npm/Yarn (Node.js)

## Installation and Setup

NexaCore-POS can be set up using Docker in two ways: **Quick Setup (Recommended for Docker)** or **Web Installer Setup**.

### Prerequisites

*   Docker Desktop (or Docker Engine and Docker Compose) installed on your system.

### Option 1: Quick Docker Setup (Recommended)

This method bypasses the web installer and sets up the application directly through command line.

#### 1. Update Environment Variables

Copy the `.env.example` file to `.env` and configure it for Docker:

```bash
cp .env.example .env
```

Open the `.env` file and ensure these settings:

```env
# Skip the web installer for Docker setup
INSTALLATION_STATUS=true

# Application settings
APP_NAME="NexaCore POS"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

# Database configuration for Docker
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=postgres
DB_PASSWORD=postgres

# Mail configuration (optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="NexaCore POS"
```

#### 2. Build and Start the Application

```bash
# Build and start containers
make dev

# Or manually:
make build
make up
make install
make key
make migrate
make seed
```

#### 3. Access the Application

Once setup is complete, access the application at `http://localhost`

**Default Admin Credentials:**
- Email: `admin@example.com`
- Password: `12345678`

### Option 2: Web Installer Setup

If you prefer to use the web installer interface:

#### 1. Update Environment Variables

```bash
cp .env.example .env
```

Configure the `.env` file:

```env
# Enable web installer
INSTALLATION_STATUS=false

# Database configuration
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

#### 2. Start the Application

```bash
make dev
```

#### 3. Complete Web Installation

1. Navigate to `http://localhost/install`
2. Follow the installation wizard:
   - **Requirements**: Verify server requirements
   - **Permissions**: Confirm directory permissions
   - **Environment**: Configure application settings
     - App Name: `NexaCore POS`
     - Database: Use the PostgreSQL settings above
     - Admin Email: Your email address
     - **Envato Fields**: Leave empty (optional - username/purchase code not required)
   - **Final**: Complete installation

#### 4. Post-Installation

After completing the web installer, update your `.env` file:

```env
INSTALLATION_STATUS=true
```

### Using the Makefile

A `Makefile` has been created to simplify common Docker commands. For detailed instructions on how to use the `Makefile` and its various commands (development, production, build, up, down, install, key, migrate, artisan, clean), please refer to the <mcfile name="make-steps.md" path="/Users/karimhamadeh/Dev/NexaCore-POS/make-steps.md"></mcfile> file.

### Troubleshooting

- **Permission Errors**: Ensure Docker has proper file permissions
- **Database Connection**: Verify PostgreSQL container is running with `docker ps`
- **Port Conflicts**: Change the port in `docker-compose.yml` if port 80 is in use
- **Installer Issues**: Use Option 1 (Quick Setup) to bypass web installer completely

## Usage

(Instructions on how to use the application's features would typically go here.)

## Contributing

(Guidelines for contributing to the project would typically go here.)

## License

(Information about the project's license would typically go here.)
