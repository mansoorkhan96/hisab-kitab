# Hisab Kitab - Agricultural Management System

This is a Laravel-based agricultural management system designed for Sindhi landlords and farmers to track farming operations using local terminology and practices.

## Project Overview

- **Framework**: Laravel 12 with PHP 8.2+
- **Admin Panel**: Filament v4
- **Database**: SQLite (based on migration files)
- **Testing**: Pest
- **Code Quality**: Laravel Pint

## Key Features

- **Crop Season Management**: Track farming seasons with wheat-specific rates
- **Resource Management**: Handle farming inputs (seeds, fertilizers, machinery)
- **Financial Tracking**: Ledger system for expenses and loans
- **Harvest Operations**: Track threshing with tractors and wheat output
- **Stock Management**: Inventory tracking with suppliers
- **Local Units**: Uses Sindhi agricultural terms like "kudhi" and "kamdari"

## Main Models

- `Calculation`: Core farming calculations
- `CropSeason`: Seasonal farming periods
- `FarmingResource`: Agricultural inputs and equipment
- `ResourceStock`: Inventory management
- `Tractor`: Machinery tracking
- `User`: Multi-tenant with teams
- `Expense`: Financial tracking
- `Ledger`: Financial records
- `Loan`: Credit management

## Development Commands

- `composer run dev`: Start development environment (server, queue, logs, vite)
- `composer run test`: Run test suite
- `php artisan test`: Run specific tests
- `./vendor/bin/pint`: Code formatting

## File Structure

- **Models**: `app/Models/` - Eloquent models with team-based scoping
- **Filament Resources**: `app/Filament/Resources/` - Admin panel components
- **Migrations**: `database/migrations/` - Database schema
- **Tests**: `tests/` - Pest test files

## Notes

- Multi-tenant architecture with team-based data isolation
- Heavy use of Filament for admin interface
- Local Sindhi agricultural terminology integration
- Focus on wheat farming operations