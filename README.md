# CryptoHub - Paper Trading Platform

A Laravel-based paper trading application for practicing cryptocurrency trading with virtual money.

## Functionalities

### User Features

- **Authentication**: Register and login to access the platform
- **Trading**: Buy and sell cryptocurrency assets with virtual money
- **Portfolio Tracking**: View your holdings and current portfolio value
- **Trade History**: See all your past buy/sell transactions
- **Percentage Selling**: Quick sell options (25%, 50%, 75%, 100% of holdings)

### Admin Features

- **Asset Management**: Create, edit, and delete cryptocurrency assets
- **CoinGecko Integration**: Link assets to CoinGecko for automatic price updates
- **Price Management**: Automatic price updates every 5 minutes from CoinGecko API (when scheduler is running)

## Admin Login

- **Email**: `admin@ehb.be`
- **Password**: `Password!321`

## Quick Setup

```bash
composer install
php artisan key:generate
php artisan migrate:fresh --seed
```

## Running the Application

### Starting the Scheduler

To enable automatic price updates, run the scheduler in a separate terminal:

```bash
php artisan scheduler:run
```

This will:
- Update asset prices every 5 minutes automatically from CoinGecko
- Run continuously until you stop it (Ctrl+C)
- Only runs when you explicitly start it (no crontab needed)

**Note**: Keep this command running while you're using the application. Prices will update automatically every 5 minutes.

## Database

### Database Type
- **SQLite** (default) - Database file: `database/database.sqlite`

### Tables

**users**
- `id`, `name`, `email`, `password`, `is_admin`, `username`, `date_of_birth`, `profile_picture`, `about_me`, `timestamps`

**assets**
- `id`, `name`, `symbol`, `coingecko_id`, `price`, `price_last_updated_at`, `timestamps`

**trades**
- `id`, `user_id`, `asset_id`, `type` (buy/sell), `amount`, `price_snapshot`, `timestamps`

**news**
- `id`, `title`, `image`, `content`, `publication_date`, `timestamps`

**faq_categories**
- `id`, `name`, `order`, `timestamps`

**faq_items**
- `id`, `faq_category_id`, `question`, `answer`, `order`, `timestamps`

### Default Admin User
- **Email**: `admin@ehb.be`
- **Password**: `Password!321`
- **Role**: Admin

### Migrations
Run migrations with:
```bash
php artisan migrate
```

Reset database with:
```bash
php artisan migrate:fresh --seed
```
