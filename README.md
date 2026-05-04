# Project Structure (Organized)

This workspace is organized into clear sections:

- Frontend: `frontend/`
- Backend (Laravel API): `backend/`
- Database layer (migrations/models): `backend/database/` and `backend/app/Models/`

> Note: `backend/` points to the Laravel project directory.

## 1) Frontend

Location: `frontend/`

Main files:
- `frontend/index.html`
- `frontend/login.html`
- `frontend/request.html`
- `frontend/script.js`
- `frontend/style.css`

## 2) Backend

Location: `backend/`

Main API routes:
- `POST /api/login`
- `POST /api/register`
- `POST /api/logout` (auth)
- `GET /api/areas`
- `GET /api/needs`
- `POST /api/needs` (auth)

## 3) Database

Location:
- Migrations: `backend/database/migrations/`
- Models: `backend/app/Models/`

Current entities:
- `areas`
- `needs`
- `users`

## Run Instructions

### Backend

1. Open terminal in `backend/`
2. Run:

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

API base URL: `http://127.0.0.1:8000/api`

### Frontend

Open `frontend/index.html` in browser.

The frontend uses this API URL by default in `frontend/script.js`:
- `http://127.0.0.1:8000/api`

You can override at runtime via browser console:

```js
localStorage.setItem("apiBaseUrl", "http://127.0.0.1:8000/api")
```
