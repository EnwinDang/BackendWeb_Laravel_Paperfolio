# CryptoHub - Paper Trading Platform

A Laravel-based paper trading application for practicing cryptocurrency trading with virtual money.

## Setup Instructions

### Prerequisites
- PHP 8.1 or higher
- Composer
- SQLite (default) or MySQL/PostgreSQL

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd cryptohub
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Storage link (for images)**
   ```bash
   php artisan storage:link
   ```

6. **Start the development server**
   
   **If using Laravel Herd:**
   - No need to run `php artisan serve`
   - Application is available at `http://cryptohub.test` (or your project name)
   
   **If not using Herd:**
   ```bash
   php artisan serve
   ```
   - Application will be available at `http://localhost:8000`

## Login Credentials

### Admin Account
- **Email**: `admin@ehb.be`
- **Password**: `Password!321`
- **Role**: Admin

### Demo User Accounts
- **Alice Johnson**
  - Email: `alice@example.com`
  - Password: `password`
  - Username: `alice_trader`
  - Has trades, watchlist, and messages

- **Bob Smith**
  - Email: `bob@example.com`
  - Password: `password`
  - Username: `bob_crypto`
  - Has trades and watchlist

- **Charlie Brown**
  - Email: `charlie@example.com`
  - Password: `password`
  - Username: `charlie_b`
  - New trader with minimal activity

## Features

### 🔐 Authentication System
- User registration
- User login with "Remember me" functionality
- Password reset (forgot password)
- Two user roles: Regular User and Admin
- Admin can promote/revoke admin rights
- Admin can manually create users

### 👤 Profile Management
- Public profile pages (accessible without login)
- Users can edit their own profile
- Profile fields:
  - Username (display name)
  - Date of birth
  - Profile picture (stored on server)
  - "About me" text
- Trading history visible to logged-in users and admins

### 📰 News System
- Public news overview page
- Public news detail page with comments
- Admin can create, edit, and delete news items
- News items contain:
  - Title
  - Excerpt (preview text for feed)
  - Image (stored on server)
  - Content
  - Publication date

###  FAQ System
- Public FAQ page
- Questions grouped by categories
- Admin can:
  - Create, edit, delete categories
  - Create, edit, delete questions and answers
- FAQ visible to all visitors

###  Contact System
- Public contact form (accessible to all visitors)
- Any visitor can submit the form
- Admin receives email notification with message contents
- Admin can view, respond to, and manage submissions

### Paper Trading System (Extra Feature)
- Users start with $1,000 virtual cash
- Buy and sell cryptocurrency assets
- Real-time price updates from CoinGecko API
- Portfolio tracking
- Trade history
- Leaderboard based on realized profit percentage
- Watchlist: Users can add assets to their watchlist to track them

### Messaging System (Extra Feature)
- Private messaging between users
- Chat-style interface
- Message history

### Leaderboard (Extra Feature)
- Shows best traders based on realized profit percentage
- Weekly rankings

## Database Structure

### Tables

**users**
- `id`, `name`, `email`, `password`, `is_admin`, `username`, `date_of_birth`, `profile_picture`, `about_me`, `remember_token`, `timestamps`

**assets**
- `id`, `name`, `symbol`, `coingecko_id`, `price`, `price_last_updated_at`, `timestamps`

**trades**
- `id`, `user_id`, `asset_id`, `type` (buy/sell), `amount`, `price_snapshot`, `timestamps`

**news**
- `id`, `title`, `excerpt`, `image`, `content`, `publication_date`, `timestamps`

**news_comments**
- `id`, `news_id`, `user_id`, `content`, `timestamps`

**faq_categories**
- `id`, `name`, `order`, `timestamps`

**faq_items**
- `id`, `faq_category_id`, `question`, `answer`, `order`, `timestamps`

**contact_submissions**
- `id`, `name`, `email`, `subject`, `message`, `read`, `admin_response`, `responded_at`, `timestamps`

**messages**
- `id`, `sender_id`, `recipient_id`, `content`, `read`, `timestamps`

**asset_user** (Watchlist pivot table)
- `id`, `user_id`, `asset_id`, `timestamps`

### Relationships

**One-to-Many:**
- User → Trades
- User → NewsComments
- User → Messages (as sender and recipient)
- News → NewsComments
- Asset → Trades
- FaqCategory → FaqItems

**Many-to-Many:**
- User ↔ Asset (Watchlist) - Users can watch multiple assets, assets can be watched by multiple users

## Running the Scheduler

To enable automatic price updates from CoinGecko API, run the scheduler in a separate terminal:

```bash
php artisan schedule:run
```

This will:
- Update asset prices every 5 minutes automatically
- Run continuously until you stop it (Ctrl+C)
- Only runs when you explicitly start it (no crontab needed)

**Note**: Keep this command running while you're using the application. Prices will update automatically every 5 minutes.

## Database Type

- **SQLite** (default) - Database file: `database/database.sqlite`
- Can be configured to use MySQL/PostgreSQL in `.env` file

## Artisan Commands

- `php artisan assets:seed` - Seed default cryptocurrency assets
- `php artisan assets:update-prices` - Update asset prices from CoinGecko
- `php artisan users:reset` - Reset all users by deleting all trades

## Security Features

- CSRF protection on all forms
- XSS protection via Blade escaping
- Client-side validation (HTML5)
- Server-side validation
- Password hashing
- Authentication middleware
- Admin-only routes protection

## Technical Stack

- **Framework**: Laravel 11
- **Database**: SQLite (default)
- **Frontend**: Blade templating
- **API Integration**: CoinGecko API for cryptocurrency prices
- **Email**: Laravel Mail (configure in `.env`)

## Sources Used

- **Laravel Documentation**: https://laravel.com/docs
- **CoinGecko API**: https://www.coingecko.com/en/api
- **Blade Templating**: Laravel Blade documentation
- **Bootstrap/HTML/CSS**: Standard web technologies

## Email Configuration

### For Development (Log Driver - Default)
By default, emails are logged to `storage/logs/laravel.log` instead of being sent. This is perfect for development and testing.

**To view password reset emails:**
1. Request a password reset
2. Check `storage/logs/laravel.log` for the email content
3. Copy the reset link from the log file

**Configuration in `.env`:**
```env
MAIL_MAILER=log
APP_URL=http://cryptohub.test
```

**Note:** If using Laravel Herd, your URL will be `http://cryptohub.test` (or your project name). If using `php artisan serve`, use `http://localhost:8000`.

### For Production (SMTP)
To actually send emails, configure SMTP in your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@cryptohub.test
MAIL_FROM_NAME="${APP_NAME}"
APP_URL=https://yourdomain.com
```

**Popular SMTP Services:**
- **Mailtrap** (for testing): https://mailtrap.io
- **Gmail**: Use Gmail SMTP settings
- **SendGrid**: https://sendgrid.com
- **Mailgun**: https://mailgun.com

**Important:** Make sure `APP_URL` in `.env` matches your actual domain, as it's used to generate password reset links.

## Important Notes

- All images are stored in `storage/app/public/` and linked via `storage:link`
- Email configuration must be set in `.env` for emails to work
- For development, use `MAIL_MAILER=log` and check `storage/logs/laravel.log` for emails
- The scheduler must be running for automatic price updates
- Default assets are seeded automatically with `migrate:fresh --seed`
- All users start with $1,000 virtual cash for paper trading

## Testing the Application

After running `php artisan migrate:fresh --seed`, you should be able to:

1.  Log in as admin (admin@ehb.be / Password!321)
2.  View and manage users
3.  Create, edit, and delete news items
4.  Manage FAQ categories and items
5.  View contact submissions
6.  View public profiles
7.  Test contact form (as non-admin user)
8.  Use all extra features (trading, messaging, leaderboard)
9.  Navigate without errors

### Testing Password Reset

**With Log Driver (Development):**
1. Go to the login page and click "Forgot your password?"
2. Enter a valid email (e.g., `alice@example.com`)
3. Check `storage/logs/laravel.log` for the password reset email
4. Copy the reset link from the log file
5. Open the link in your browser to reset the password

**With SMTP (Production):**
1. Configure SMTP in `.env` (see Email Configuration above)
2. Request password reset - email will be sent to the user's inbox
3. Click the reset link in the email
4. Set a new password

**Note:** Make sure `APP_URL` in `.env` matches your actual domain/URL for reset links to work correctly.

## Git Repository

- Repository must be on GitHub
- Regular commits with clear messages
- `.gitignore` includes: `vendor/`, `node_modules/`, `.env`

---

**Project Status**: ✅ All required features implemented and tested
