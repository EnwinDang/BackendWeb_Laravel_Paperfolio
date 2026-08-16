# PaperFolio - Paper Trading Platform

Een Laravel-applicatie voor het oefenen van cryptotrading met virtueel geld.

## Built With

* [Laravel 11](https://laravel.com) — backend framework
* [Blade](https://laravel.com/docs/blade) — templating
* SQLite — standaard database
* [CoinGecko API](https://www.coingecko.com/en/api) — live cryptoprijzen
* [TradingView widget](https://www.tradingview.com/widget/advanced-chart/) — grafieken op de asset-pagina
* **Claude (Anthropic)** — hielp bij het bouwen en debuggen van features, live testen in
  de browser, en het schrijven van deze documentatie

## Aan de slag

```bash
git clone
cd paperfolio
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Applicatie draait op `http://localhost:8000` (of `http://paperfolio.test` met Laravel Herd).

**Optioneel — koppeling met project 2 (Price Alerts API):** zet in `.env`
`PRICE_ALERTS_API_URL` en `PRICE_ALERTS_API_KEY` (zelfde key als in het Node-project).
Zonder deze stap werkt de rest van de app gewoon door — de Price Alerts-pagina toont dan
netjes "tijdelijk niet beschikbaar".

## Inloggegevens

- **Admin**: `admin@ehb.be` / `Password!321`
- **Demo-gebruiker**: `alice@example.com` / `password` (ook `bob@example.com` en
  `charlie@example.com`, zelfde wachtwoord)

## Features

- Registreren, inloggen, wachtwoord resetten
- Admin: gebruikers beheren (aanmaken, admin maken, schorsen, gegevens wissen)
- Publiek profiel per gebruiker, zelf te bewerken
- Nieuws en FAQ, beheerbaar door admin
- Contactformulier, met notificatie + reactiemogelijkheid voor admin
- Notificaties (belicoon) voor belangrijke gebeurtenissen
- Paper trading: kopen/verkopen, leveraged posities, watchlist, portfolio
- Social feed met zoeken, paginatie en liken zonder herladen; admin kan posts
  verwijderen met opgave van reden
- Privéberichten tussen gebruikers
- Leaderboard
- Price Alerts, via de aparte Node.js API uit project 2

## Assetprijzen verversen

`migrate:fresh --seed` haalt meteen live prijzen op. Om ze te blijven verversen (elke 5
minuten): zet de Scheduler-toggle aan in Laravel Herd, of draai handmatig in een aparte
terminal:
```bash
php artisan scheduler:run
```
Of eenmalig manueel: `php artisan assets:update-prices`

## Bronnen

- Laravel documentatie — https://laravel.com/docs
- CoinGecko API — https://www.coingecko.com/en/api
- TradingView widget — https://www.tradingview.com/widget/advanced-chart/
- Claude (Anthropic) — AI-assistentie, zie "Built With" hierboven
