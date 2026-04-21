# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.3] - 2026-04-21
### Fixed
- **SQL Error 1366**: Resolved `Incorrect integer value: ''` crashes on the live app by converting empty form strings to `NULL` for `package_id` and other integer/date columns in `CustomerController`.
- **Database Connection**: Improved database connection reliability by enabling `PDO::ERRMODE_EXCEPTION` and separating credentials into `config/database.php`.

### Changed
- **Config Management**: Removed `config/database.php` from `.gitignore` for easier project deployment (upon user request).
- **Core Refactoring**: Reorganized `app/Core/Database.php` to use constants defined in the configuration file.

## [1.1.2] - 2026-02-20
### Changed
- **Customer Form Builder**: Improved handling of dropdown options, ensuring they are correctly parsed and persisted in the setup UI.
- **User Sync**: Enhanced `AuthController` and `EmployeeController` to better synchronize employee data with user accounts and handle potential errors gracefully.

## [1.1.1] - 2026-02-13
### Changed
- **Field Key Generation**: Improved the uniqueness of custom field keys in the form builder using `random_bytes`.
- **UI Enhancements**: Added visual feedback `(new_field_unsaved)` in the Customer Form Setup to clearly distinguish newly added, unsaved fields.

## [1.1.0] - 2026-02-01
### Added
- **Dynamic Customer Form Builder**: Integrated a customizable form system for customer creation.
- **Form Setup Interface**: Added a management UI under `Setup > Customer Form Setup` to add, reorder, and toggle visibility of fields and sections.
- **Custom Field Support**: Implemented a `customer_meta` system to store extensible customer data without affecting the main table schema.
- **Enhanced Specific Sections**: Added deep support for "Mikrotik Configuration" and "Official Information" with pre-defined sections.
- **Field Placeholders**: Added a `placeholder` column to the form builder to allow custom visual hints (e.g., IP address, MAC formats) for all inputs.

### Changed
- Refactored `customer/create` and `customer/show` to render fields dynamically based on database configuration.
- Improved URL routing in `App.php` to handle hyphenated and underscored paths (`customer-form` vs `customer_form`) consistently.

### Fixed
- Fixed PHP undefined array key warnings during dynamic form rendering.
- Removed intrusive default "0" values in numeric inputs to allow placeholders to be visible.
- Resolved database seeding inconsistencies for Mikrotik and Official sections.

## [1.0.0] - 2026-01-26
### Added
- Initial implementation of versioning system.
- Automated changelog tracking.
- Displayed app version in the footer.
- Established basic project structure with MVC architecture.
- Added user authentication system (Login/Register).
- Implemented user profile and dashboard.
- Setup wizard for initial configuration.
- Customer management core.
