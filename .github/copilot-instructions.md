# Project Guidelines

## Code Style
- **Language**: PHP with Blade templating
- **Formatting**: Use `JSON_PRETTY_PRINT` for JSON storage files
- **Naming**: Use Indonesian terms for business concepts (alat, kategori, pembayaran, peminjaman); helper files as `*_helper.php`
- Reference: [auth.php](routes/auth.php) for authentication patterns, [payment_helper.php](routes/payment_helper.php) for CRUD operations

## Architecture
- **Components**: Route handlers in `/routes/*.php`, business logic in `*_helper.php`, data in `/storage/*.json`, views in `/assets/resources/views/*.blade.php`
- **Boundaries**: File-based MVC variant with helpers handling data operations; controllers exist but are stubs
- **Decisions**: JSON file persistence for simplicity; session-based authentication; Midtrans for payments
- Reference: [PAYMENT_SYSTEM.md](PAYMENT_SYSTEM.md) for payment flow and data structures

## Build and Test
- No build system required; edit PHP files directly
- Run with PHP built-in server: `php -S localhost:8000 -t .`
- No automated tests present

## Conventions
- **Data Persistence**: CRUD functions in helpers read/write JSON files
- **Authentication**: Session-based with role checks (`admin` vs `user`)
- **Routing**: Each `.php` in `/routes/` handles a page with embedded HTML
- **Differences**: No database or framework; manual validation; Indonesian terminology</content>
<parameter name="filePath">/workspaces/olivcart/.github/copilot-instructions.md