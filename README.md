# PaperFolio - Paper Trading Platform

Een Laravel-applicatie voor het oefenen van cryptotrading met virtueel geld.

## Over het Project

PaperFolio is een paper-trading platform: gebruikers krijgen virtueel geld en kunnen
daarmee cryptomunten kopen/verkopen, leveraged posities openen, en hun resultaten volgen
op een leaderboard. Daarnaast heeft het platform een sociale kant (feed, berichten,
profielen) en een volledig admin-panel voor het beheren van gebruikers, nieuws en FAQ.

## Vereisten

- PHP >= 8.1
- Composer
- SQLite (standaard) of MySQL/PostgreSQL
- Git

## Installatie

### Stap 1: Clone de Repository
```bash
git clone <repository-url>
cd paperfolio
```

### Stap 2: Installeer Dependencies
```bash
composer install
```

### Stap 3: Kopieer Environment File
```bash
cp .env.example .env
```

### Stap 4: Genereer Application Key
```bash
php artisan key:generate
```

## Database Setup

### Stap 1: Configureer de Database
Standaard gebruikt dit project SQLite, geen extra configuratie nodig. Voor MySQL/PostgreSQL,
pas de `DB_*`-variabelen in `.env` aan.

### Stap 2: Run Migrations en Seeders
Dit maakt alle tabellen aan en vult ze met test data (inclusief live prijzen via CoinGecko):
```bash
php artisan migrate:fresh --seed
```

### Stap 3: Maak de Storage Link
```bash
php artisan storage:link
```

### Stap 4: Start de Development Server
```bash
php artisan serve
```

De applicatie is nu beschikbaar op `http://localhost:8000` (of `http://paperfolio.test`
met Laravel Herd).

## Configuratie

### Optioneel — Koppeling met Project 2 (Price Alerts API)
Zet in `.env`:
```env
PRICE_ALERTS_API_URL=http://localhost:4000
PRICE_ALERTS_API_KEY=zelfde-key-als-in-het-node-project
```
Zonder deze stap werkt de rest van de applicatie gewoon door — de Price Alerts-pagina
toont dan netjes "tijdelijk niet beschikbaar" in plaats van een foutmelding.

### E-mail (Optioneel)
Standaard worden e-mails gelogd naar `storage/logs/laravel.log` in plaats van verstuurd:
```env
MAIL_MAILER=log
```
Voor echte e-mails (bv. via Mailtrap):
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=jouw_mailtrap_username
MAIL_PASSWORD=jouw_mailtrap_password
MAIL_ENCRYPTION=tls
```

## Login Credentials

### Admin Account
- Email: `admin@ehb.be`
- Password: `Password!321`

### Demo-gebruikers
- `alice@example.com` / `password`
- `bob@example.com` / `password`
- `charlie@example.com` / `password`

## Features

### Authenticatie
- Registreren, inloggen (met "Onthoud mij"), wachtwoord resetten
- Twee rollen: gewone gebruiker en admin
- Admin kan gebruikers aanmaken, admin maken/terugzetten, schorsen, en gegevens wissen
  (GDPR-stijl: naam/e-mail/foto worden geanonimiseerd, trades/posts blijven bestaan zodat
  andermans data — zoals hun kant van een gesprek — niet kapot gaat)

### Profiel
- Publiek profiel per gebruiker, zelf te bewerken (username, foto, geboortedatum, bio)
- Privacy-instellingen per gebruiker (leeftijd, e-mail, handelsgeschiedenis tonen of niet)

### Nieuws & FAQ
- Publiek nieuwsoverzicht met detailpagina en reacties
- FAQ gegroepeerd per categorie
- Beide volledig beheerbaar door admin (CRUD, inclusief afbeeldingen bij nieuws)

### Contact & Notificaties
- Publiek contactformulier, toegankelijk voor iedereen
- Admin krijgt een e-mail én een in-app notificatie (belicoon) bij een nieuw bericht,
  met directe link naar de submission en reactiemogelijkheid
- Notificatie-overzichtspagina, apart of allemaal als gelezen te markeren

### Trading
- Kopen/verkopen van cryptomunten met virtueel geld ($1.000 startkapitaal, zelf te
  resetten vanuit het profiel)
- Leveraged long/short posities (5x, 10x, 100x), margin-gebaseerd met automatische
  liquidatie
- Portfolio-overzicht, handelsgeschiedenis, watchlist
- Leaderboard op gerealiseerd winstpercentage, met week-/maand-/jaarweergave

### Social
- Feed met posts, `$cashtag`-links naar assets (bv. `$BTC`), liken via AJAX zonder
  pagina-herlaad
- Zoeken op gebruiker en op tekst, met paginatie van de resultaten
- Admin kan geen posts plaatsen of liken (enkel modereren), en kan ongepaste posts
  verwijderen met opgave van reden — de auteur krijgt dit als notificatie
- Privéberichten tussen gebruikers

### Price Alerts (koppeling met Project 2)
- Gebruikers stellen een alert in (asset, richting, doelprijs) en zien wanneer die
  getriggerd wordt
- Communicatie met de aparte Node.js API loopt volledig over HTTP, geen gedeelde
  database
- Bij het verwijderen van een alert controleert Laravel of het e-mailadres overeenkomt
  met de ingelogde gebruiker, aangezien de Node-API enkel de API key controleert
- Werkt de Node-API niet? Dan toont deze pagina een nette foutmelding in plaats van te
  crashen

## Technologieën

**Backend**
- Laravel 11
- PHP 8.1+
- SQLite (standaard)
- Eloquent ORM

**Frontend**
- Blade Templates
- Eigen CSS (geen framework zoals Bootstrap/Tailwind)

**Externe API's**
- CoinGecko API — live cryptoprijzen
- TradingView widget — grafieken op de asset-pagina
- Eigen Node.js Price Alerts API (project 2)

## Beveiliging

- CSRF-bescherming op alle formulieren
- XSS-bescherming via Blade escaping
- Server-side validatie op elk formulier
- Wachtwoord-hashing
- Admin-only routes afgeschermd met middleware
- Custom bevestigingsvensters bij destructieve acties (geen browser-native `confirm()`)

## Bronvermeldingen

### Documentatie
- Laravel: https://laravel.com/docs
- CoinGecko API: https://www.coingecko.com/en/api
- TradingView widget: https://www.tradingview.com/widget/advanced-chart/

### AI Assistentie
**Claude (Anthropic)**
- Gebruikt voor ondersteuning bij projectontwikkeling
- Specifieke hulp bij: het bouwen en debuggen van features (o.a. notificatiesysteem,
  schorsen/wissen van gebruikers, feed-zoekfunctie, koppeling met de Price Alerts API),
  live testen in de browser, en het schrijven van deze documentatie

## Licentie

Dit project is gemaakt voor educatieve doeleinden als onderdeel van een schoolopdracht.
