# PaperFolio - Paper Trading Platform

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
   cd paperfolio
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
   - Application is available at `http://paperfolio.test` (or your project name)
   
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
- Privacy toggles: users can individually choose whether their age, email, and portfolio/trading history are visible to other visitors (owner and admins can always see everything)

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

### ❓ FAQ System
- Public FAQ page
- Questions grouped by categories
- Admin can:
  - Create, edit, delete categories
  - Create, edit, delete questions and answers
- FAQ visible to all visitors

### ✉️ Contact System
- Public contact form (accessible to all visitors)
- Any visitor can submit the form
- Admin receives email notification with message contents
- Admin can view, respond to, and manage submissions

### Paper Trading System (Extra Feature)
- Users start with $1,000 virtual cash, self-service restart back to exactly $1,000 from their profile
- Buy and sell cryptocurrency assets (spot trading)
- Real-time price updates from CoinGecko API
- Portfolio tracking (spot holdings + leveraged positions combined)
- Trade history
- Watchlist: Users can add assets to their watchlist to track them

### Leveraged Trading (Extra Feature)
- Simplified long/short leveraged positions at 5x, 10x, or 100x
- Margin-based: losses are floored at the position's margin, so an account can never go negative from one bad trade
- Automatic liquidation once a position's losses wipe out its margin (checked on every price update)
- Open positions and closed position history shown on both the Dashboard and Portfolio pages

### Social Feed (Extra Feature)
- Twitter-style feed where users post short updates and like others' posts
- `$TICKER` cashtags in posts (e.g. `$BTC`) automatically link to that asset's trading page
- Trending posts (most-liked) surfaced on the public homepage

### Messaging System (Extra Feature)
- Private messaging between users
- Chat-style interface
- Message history

### Leaderboard (Extra Feature)
- Shows best traders ranked by realized profit percentage (not raw $ gain, so a small account can outrank a bigger one)
- Weekly, Monthly, and Yearly views, each with period navigation — resetting periodically so long-tenured traders can't just camp the top spot forever

## Database Structure

### Tables

**users**
- `id`, `name`, `email`, `password`, `is_admin`, `username`, `date_of_birth`, `profile_picture`, `about_me`, `show_portfolio`, `show_age`, `show_email`, `remember_token`, `timestamps`

**assets**
- `id`, `name`, `symbol`, `coingecko_id`, `price`, `price_change_24h`, `price_last_updated_at`, `timestamps`

**trades**
- `id`, `user_id`, `asset_id`, `type` (buy/sell), `amount`, `price_snapshot`, `timestamps`

**positions** (leveraged trades)
- `id`, `user_id`, `asset_id`, `direction` (long/short), `leverage`, `margin_usd`, `entry_price`, `close_price`, `status` (open/closed/liquidated), `realized_pnl`, `closed_at`, `timestamps`

**posts** (social feed)
- `id`, `user_id`, `content`, `timestamps`

**post_likes** (pivot table)
- `id`, `post_id`, `user_id`, `timestamps`

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
- User → Positions (leveraged trades)
- User → Posts
- User → NewsComments
- User → Messages (as sender and recipient)
- News → NewsComments
- Asset → Trades
- Asset → Positions
- FaqCategory → FaqItems

**Many-to-Many:**
- User ↔ Asset (Watchlist) - Users can watch multiple assets, assets can be watched by multiple users
- User ↔ Post (Likes) - Users can like multiple posts, posts can be liked by multiple users

## Asset Prices & the Scheduler

`php artisan migrate:fresh --seed` already fetches live prices from CoinGecko for every seeded asset as part of seeding — you don't need anything extra running just to see real prices right after setup.

To keep prices refreshing automatically while you use the app (every 5 minutes, via the schedule defined in `bootstrap/app.php`), you need something to actually trigger it continuously. Two options:

- **Laravel Herd**: enable the per-site **Scheduler** toggle (Herd app → your site → Services/Scheduler) — Herd then calls `schedule:run` every minute for you in the background, no terminal needed.
- **Manual / no Herd**: run this project's custom scheduler-loop command in a separate terminal and leave it open:
  ```bash
  php artisan scheduler:run
  ```
  (Note the name: `scheduler:run`, not Laravel's built-in `schedule:run` — the built-in one only checks and runs due tasks once, then exits immediately; `scheduler:run` is a custom command in this project that loops every 60 seconds so it keeps checking.)

If neither is running, prices simply stay at whatever they were seeded/last updated at — you can always force a refresh manually with:
```bash
php artisan assets:update-prices
```

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
- **Blade Templating**: Laravel Blade documentation
- **CoinGecko API**: https://www.coingecko.com/en/api — used for live asset prices and 24h change
- **TradingView Advanced Chart Widget**: https://www.tradingview.com/widget/advanced-chart/ — embedded on the asset trading page
- **HTML/CSS/JS**: Hand-written, no CSS framework — custom design system built with CSS custom properties (no Bootstrap/Tailwind)

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
APP_URL=http://paperfolio.test
```

**Note:** If using Laravel Herd, your URL will be `http://paperfolio.test` (or your project name). If using `php artisan serve`, use `http://localhost:8000`.

### For Production (SMTP)
To actually send emails, configure SMTP in your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@paperfolio.test
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
- Email configuration must be set in `.env` for emails to work; contact-form and password-reset emails fail silently (logged, not crashed) if mail isn't configured, so the rest of the app keeps working either way
- For development, use `MAIL_MAILER=log` and check `storage/logs/laravel.log` for emails
- Default assets and their live prices are seeded automatically with `migrate:fresh --seed` — see [Asset Prices & the Scheduler](#asset-prices--the-scheduler) if you want them to keep refreshing afterward
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
