# Workshop Management System

A full-stack workshop management application built with Laravel 13 and Vue 3 as a single-page application via Inertia.js v2.

Admins can create, edit, and delete workshops and view statistics. Employees can browse workshops, register for them, join waiting lists when workshops are full, and cancel their registrations. When a confirmed participant cancels, the first user on the waiting list is automatically promoted (FIFO).

## Tech Stack

- **Backend:** Laravel 13, PHP 8.5
- **Frontend:** Vue 3, Inertia.js v2, Tailwind CSS v4, Vite 8
- **Authentication:** Laravel Fortify (headless) with 2FA (TOTP)
- **Authorization:** Spatie Laravel Permission
- **Database:** MySQL 8.4, Redis (cache)
- **Testing:** Pest v4
- **Code Quality:** PHPStan (level 9), Laravel Pint, Rector, ESLint, Prettier
- **Dev Environment:** Laravel Sail (Docker)
- **Mail:** Mailpit (local development)
- **Route Generation:** Laravel Wayfinder

## Requirements

- Docker & Docker Compose
- Composer >= 2

## Quick Setup

### 1. Clone the repository

```bash
git clone git@github.com:paolobellini/workshop.corsi.it.git
cd workshop.corsi.it
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Configure environment

Update `.env` for Sail (MySQL + Redis):

```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

REDIS_HOST=redis

CACHE_STORE=redis

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

### 4. Run the setup script

This single command will:
- Copy `.env.example` to `.env` (if not already present)
- Start the Docker containers
- Generate the application key
- Run migrations and seed the database
- Install Node dependencies
- Build the frontend assets

```bash
composer run setup
```

The application is now available at **http://localhost**.

### Default Users

| User            | Email              | Role     | Password |
|-----------------|--------------------|----------|----------|
| Paolo Bellini   | paolo@bellini.one  | Admin    | password |
| John Doe        | doe@example.com    | Employee | password |

Plus 4 sample workshops.

## Running the Application

```bash
# Start containers
vendor/bin/sail up -d

# Stop containers
vendor/bin/sail stop
```

For frontend hot reload during development:

```bash
vendor/bin/sail npm run dev
```

The application is available at **http://localhost**.

### Mailpit

Email testing UI is available at **http://localhost:8025**.

## Docker Services

| Service    | Port | Description             |
|------------|------|-------------------------|
| App        | 80   | Laravel application     |
| App (Vite) | 5173 | Vite HMR dev server     |
| MySQL      | 3306 | MySQL 8.4 database      |
| Redis      | 6379 | Redis cache             |
| Mailpit    | 8025 | Email testing interface  |

## User Roles

### Admin

- View workshop list with statistics (total, completed, upcoming, total registrations)
- Create, edit, and delete workshops
- View workshop details with participant list

### Employee

- Browse available workshops
- Register for workshops with available seats
- Join waiting list when a workshop is full
- Cancel registration or leave the waiting list
- Automatic promotion from waiting list when a spot opens (FIFO)

## Available Commands

### Development

```bash
# Start/stop containers
vendor/bin/sail up -d
vendor/bin/sail stop

# Vite dev server (hot reload)
vendor/bin/sail npm run dev

# Build frontend for production
vendor/bin/sail npm run build
```

### Testing

```bash
# Run all tests with type coverage and code coverage (min 90%)
vendor/bin/sail composer run tests

# Run a specific test file
vendor/bin/sail artisan test --compact tests/Feature/Workshop/IndexTest.php

# Run tests matching a filter
vendor/bin/sail artisan test --compact --filter=testName
```

### Code Quality

```bash
# PHP formatting (modified files only)
vendor/bin/sail bin pint --dirty --format agent

# Static analysis (PHPStan level 9)
vendor/bin/sail composer run analyse

# Refactoring (Rector)
vendor/bin/sail composer run refactor          # Apply
vendor/bin/sail composer run check:refactor    # Dry-run

# Frontend linting
vendor/bin/sail npm run lint      # ESLint fix
vendor/bin/sail npm run format    # Prettier fix

# TypeScript type checking
npm run types:check

# Full CI-like check (lint + analyse + refactor dry-run)
vendor/bin/sail composer run check
```

### Database

```bash
# Run migrations
vendor/bin/sail artisan migrate

# Reset and re-seed
vendor/bin/sail artisan migrate:fresh --seed
```

## Project Structure

```
app/
├── Actions/              # Business logic (register, unregister, promote, etc.)
├── Enums/                # Roles enum
├── Http/
│   ├── Controllers/      # Workshop, Registration, WaitingList controllers
│   ├── Middleware/        # Inertia shared data
│   ├── Requests/         # Form request validation
│   └── Resources/        # API resources (WorkshopResource)
├── Models/               # User, Workshop, WaitingList
├── Observers/            # WorkshopObserver (cache invalidation)
└── Rules/                # NoWorkshopOverlap validation rule

resources/js/
├── components/           # Reusable Vue components
├── composables/          # Vue composables (useRole, useFlashToast, etc.)
├── layouts/              # App and Auth layouts
├── pages/
│   ├── auth/             # Login, Register, ForgotPassword, etc.
│   ├── settings/         # Profile, Security, Appearance
│   └── workshops/        # Index, Show
└── types/                # TypeScript type definitions

lang/
├── en/                   # English translations
└── it/                   # Italian translations (default locale)

tests/
├── Feature/              # Feature tests (HTTP, integration)
└── Unit/                 # Unit tests (actions, resources)
```

## Localization

The application is configured in Italian (`APP_LOCALE=it`). All validation messages, authentication messages, and UI text are translated. English translations are available as a fallback.
