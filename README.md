# BitsTree Withdrawal Module for Magento 2

EU-compliant withdrawal (Widerrufsrecht) module for Magento 2. Adds a configurable withdrawal button to the customer order view and order history, sends email notifications to customers and store owners, and optionally creates automatic credit memos.

## Features

- **Withdrawal button** on order detail page and order history list
- **Configurable withdrawal periods** with multiple calculation modes:
  - Based on order date or shipment date
  - Based on a configurable delivery date order attribute
  - Dedicated period for orders without a suitable delivery date
  - Per-shipping-method period overrides
- **SKU filtering** via regex pattern to limit withdrawal to eligible products
- **Email notifications** to customer and configurable store recipients with customizable subjects
- **Automatic credit memo** creation for eligible items (optional)
- **Multi-store** and **multi-language** support (DE/EN translations included)
- **Confirmation dialog** before submission

## Requirements

- PHP 8.1+
- Magento 2.4.6 or later (Community/Commerce Edition)

## Installation

### Via Composer (recommended)

```bash
composer require bitstree/module-withdrawal
bin/magento module:enable BitsTree_Withdrawal
bin/magento setup:upgrade
bin/magento cache:flush
```

### Manual Installation

Copy the module files to `app/code/BitsTree/Withdrawal/`, then:

```bash
bin/magento module:enable BitsTree_Withdrawal
bin/magento setup:upgrade
bin/magento cache:flush
```

## Configuration

Navigate to **Stores > Configuration > Sales > Withdrawal** in the Magento Admin.

### General

| Setting | Description |
|---|---|
| Enable Withdrawal | Enable/disable the module |
| Show on Order List | Show withdrawal button in the order history list |
| Show on Order Detail | Show withdrawal button on the order detail page |
| SKU Regex Pattern | Regex to filter eligible SKUs (e.g. `^[^1]` = SKUs not starting with "1"). Leave empty to allow all. |
| Automatic Credit Memo | Automatically create a credit memo when a withdrawal is submitted |

### Withdrawal Period

| Setting | Description | Default |
|---|---|---|
| Period Start From | Starting point for period calculation: Order Date or Shipment Date | Order Date |
| Default Withdrawal Period (Days) | Fallback period if no specific configuration matches | 14 |
| Delivery Date Order Attribute | Order attribute code containing the delivery date (e.g. `c_delivery_date`). Leave empty to disable. | — |
| Delivery Date Withdrawal Period (Days) | Period when the order has a delivery date (starts from that date) | 14 |
| No Delivery Date Shipping Method | Shipping method code for orders without a suitable delivery date (e.g. `homedelivery_other`). Leave empty to disable. | — |
| No Delivery Date Withdrawal Period (Days) | Period for orders where no suitable delivery date was selected | 20 |
| Withdrawal Period per Shipping Method | Per-shipping-method period overrides (dynamic rows) | — |

#### Period Calculation Priority

The withdrawal period is determined in the following order:

1. **No delivery date method** — If the order's shipping method matches the configured "no delivery date" method, uses the dedicated period from the order date.
2. **Delivery date attribute** — If the configured order attribute has a value, uses the delivery date period starting from that date.
3. **Shipping method override** — If a per-shipping-method period is configured, uses that period.
4. **Default period** — Falls back to the default withdrawal period, starting from either order date or shipment date (as configured).

### Email

| Setting | Description |
|---|---|
| Customer Email Subject | Subject line for customer emails. Placeholders: `{{order_id}}`, `{{customer_id}}`, `{{customer_name}}` |
| Notification Email Subject | Subject line for store notification emails (same placeholders) |
| Notification Email Recipients | Comma-separated list of email addresses |
| Email Sender | Magento email identity to send from |
| Customer Email Template | Email template for the customer |
| Store Notification Email Template | Email template for store notifications |

## Database

The module creates a `bitstree_withdrawal` table to store withdrawal requests. The schema is defined in `etc/db_schema.xml` and applied automatically during `setup:upgrade`.

## Translations

Included translations:
- `en_US` (English)
- `de_DE` (German)

To add further translations, create `i18n/<locale>.csv` in the module directory or override via your theme.

## License

Proprietary — Copyright BitsTree GmbH. All rights reserved.