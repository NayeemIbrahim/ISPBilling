# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
