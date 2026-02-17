# SR- The Clothing Brand

Full-stack ecommerce built with Laravel + Vue, Dockerized with MySQL.

## Environment setup
Create a root `.env` file for Docker secrets:
```bash
cp .env.example .env
```
Then set `MYSQL_PASSWORD` and `MYSQL_ROOT_PASSWORD`.

Copy backend env file and set DB password:
```bash
cp backend/.env.example backend/.env
```
Set `DB_PASSWORD` to match `MYSQL_PASSWORD`.

## Requirements
- Docker Desktop
- Node 20+ (for local frontend dev)

## Quick start
```bash
# from /Users/sohrab/Files/SR
docker compose up -d --build
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

The API will be available at `http://localhost:8000`.

## Filament admin panel
Filament runs on the Laravel backend at:
- `http://localhost:8000/admin`

Use the admin credentials below to log in. Access is restricted to users with `is_admin = 1`.

## Frontend (local dev)
```bash
cd frontend
npm install
npm run dev
```

Frontend runs at `http://localhost:5173` and uses the API at `http://localhost:8000/api`.

## Frontend (Docker)
```bash
docker compose --profile frontend up node
```

## Default users
- Admin: `admin@sr-clothing.test` / `password`
- Customer: `customer@sr-clothing.test` / `password`

## Notes
- Auth uses Laravel Sanctum (SPA cookie). The frontend calls `/sanctum/csrf-cookie` before login/register.
- Admins can manage products and update order delivery status.
