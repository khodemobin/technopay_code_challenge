## Wallet-Based Purchase System


This project focuses on implementing a wallet-based invoice payment system on Laravel Version 12 that guarantees strong consistency and concurrency safety. Features include two-step verification, user spending limits, transaction completion alerts, and real-time spending limit notifications.

### Features

- Enhanced security measures for paying invoices through user wallets.
- Two-step verification (2SV) integrated before payment.
- Prevent Unauthorized payments with global daily spending limit enforcement.
- Checks on wallet balance and usability.
- Precise timestamps for payments.
- Alerts and notifications for users post payment regarding success or failure.
- Refund logic and automatic adjustments for failed payments.
- Secure management of concurrent transactions.

### Architecture

- PayInvoiceService: Primary service that orchestrates the payment flow.
- InvoiceService: Validating and managing invoice states.
- WalletService: Validation of wallet balance and usability.
- DailySpendingLimitService: Check and apply daily spending limit.
- TwoStepVerificationService: Initiates and verifies 2SV.
- NotificationService: Pays and alerts users regarding payment.

### Design Patterns & Principles

- Service Layer: Business logic is stored in different services which are managed separately from the controllers/models.
- Dependency Injection: Tests and alterations are easily made as services and controllers can be altered through their constructors.
- Single Responsibility Principle (SRP): Each service handles a single aspect of the payment process.
- Transaction Management: Uses database transactions for atomic wallet deduction and invoice marking.
- Exception Handling: Custom exceptions like PaymentException signal domain errors.


### Installation & Setup

- Clone the repo
- Run ```composer install```
- Configure .env (DB connection, daily spending limit, etc.)
- Run migrations: ```php artisan migrate```
- Seed initial data: ```php artisan db:seed```
- Serve with ```php artisan serve```

### Running Tests
Run all tests with:

```php artisan test```


### API Endpoints

- POST /api/invoice/{invoice}/pay
```
{
    "code": "two-step-verification-code"
}
```

- POST /api/invoice/{invoice}/2sv/initiate
- POST /api/invoice/{invoice}/2sv/verify


Notifications are logged; replace with real email/SMS logic as needed.
SQLite is supported, but ensure your DB supports transactions properly for concurrency safety.
