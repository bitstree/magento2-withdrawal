# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.2] - 2026-04-01

### Fixed
- PHPDoc annotations in `WithdrawalManagementInterface` now use fully qualified class names (FQCNs) for `@return` and `@throws` tags, ensuring correct Magento REST API schema generation via reflection

## [1.1.1] - 2026-03-23

### Fixed
- Withdrawal deadline calculation for orders with delivery date now respects shipping-method-specific withdrawal periods instead of always using the generic delivery date period

## [1.1.0] - 2026-03-12

### Added
- REST API for withdrawal management (`etc/webapi.xml`)
  - `POST /V1/withdrawal/order/:orderId` — Admin: create withdrawal for any order
  - `POST /V1/withdrawal/mine/order/:orderId` — Customer: create withdrawal for own order
  - `GET /V1/withdrawal/mine/eligible-orders` — Customer: list orders eligible for withdrawal
- `WithdrawalManagementInterface` service contract with `createByOrderId`, `createByOrderIdForCustomer`, and `getEligibleOrders`
- Configurable **Company Name** setting (`general/company_name`) to replace hardcoded company references in confirmation dialogs and email templates
- REST API documentation section in README

### Changed
- Refactored `Controller/Index/Submit` to delegate business logic to `WithdrawalManagement` service
- Email templates now use `{{var company_name}}` template variable instead of hardcoded company name

## [1.0.0] - 2026-03-09

### Added
- Withdrawal button on order detail page and order history list
- Configurable withdrawal periods (order date, shipment date, delivery date, per shipping method)
- Delivery date order attribute support with configurable period
- Dedicated period for "no delivery date" shipping method
- SKU regex filtering for eligible products
- Customer and store notification emails with configurable subjects
- Automatic credit memo creation (optional)
- German (de_DE) and English (en_US) translations
- Admin configuration under Stores > Configuration > Sales > Withdrawal