# Setup

Complete guide to initialize the Laravel template locally.

## Prerequisites

- PHP `^8.4`
- Composer
- Bun (recommended) or NPM
- Local database (SQLite, MySQL, or PostgreSQL)

## 1) Install backend dependencies

```bash
composer install
```

## 2) Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and configure at least:

- database connection (`DB_*`)
- cache/session/queue drivers based on your environment

## 3) Database

```bash
php artisan migrate
```

If you have seeders to run:

```bash
php artisan db:seed
```

## 4) Install frontend dependencies

With Bun:

```bash
bun install
```

With NPM:

```bash
npm install
```

## 5) Build frontend

With Bun:

```bash
bun run build
```

With NPM:

```bash
npm run build
```

## 6) Start the application

Simple mode:

```bash
php artisan serve
```

Full development mode (HTTP server, queue, reverb, vite):

```bash
composer run dev
```

## Quick troubleshooting

- Application key error: run `php artisan key:generate` again.
- Missing assets: run `bun run build` or `npm run build` again.
- Cache/config issues: `php artisan optimize:clear`.
