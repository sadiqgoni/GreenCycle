# GreenCycle

A modern waste management and recycling platform built with Laravel and Filament.

## Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/PostgreSQL

## Installation

1. Clone the repository
```bash
git clone https://github.com/sadiqgoni/GreenCycle.git
cd GreenCycle
```

2. Install PHP dependencies
```bash
composer install
```

3. Install Node.js dependencies
```bash
npm install
```

4. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in the `.env` file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=greencycle
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations
```bash
php artisan migrate
```

7. Build assets
```bash
npm run build
```

## Development

1. Start the Laravel development server
```bash
php artisan serve
```

2. Watch for asset changes
```bash
npm run dev
```

## Features

- Multi-tenant architecture with separate panels for:
  - Admin Dashboard
  - Company Management
  - Household Service Requests
- Revenue tracking and analytics
- Service request management
- Bidding system
- Interactive charts and widgets

## Directory Structure

- `/app` - Application core logic
- `/resources` - Frontend assets and views
- `/database` - Migrations and seeders
- `/config` - Configuration files
- `/routes` - Application routes

## Testing

Run the test suite:
```bash
php artisan test
```

## License

This project is licensed under the MIT License.
