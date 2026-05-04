# Humanitarian Tracking Backend

منصة Laravel + Filament لإدارة الاحتياجات الإنسانية مع دعم API للموبايل، تنبؤات تحليلية، تكامل OCHA، وسجل تبرعات شفاف.

## Quick Start

1. تثبيت الاعتماديات:

```bash
php composer.phar install --ignore-platform-reqs
```

2. إعداد البيئة:

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

3. التشغيل:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

## Main URLs

- Admin Panel: `http://127.0.0.1:8000/admin`
- API Base: `http://127.0.0.1:8000/api`
- API Docs UI: `http://127.0.0.1:8000/api-docs`

## Auth API

- `POST /api/register`
- `POST /api/login`
- `POST /api/logout` (auth:sanctum)
- `POST /api/registration-status`
- `GET /api/me` (auth:sanctum)

## Needs & Areas API

- `GET /api/areas`
- `GET /api/areas/{id}`
- `GET /api/needs`
- `GET /api/needs/pending`
- `GET /api/needs/{id}`
- `POST /api/needs` (auth:sanctum)

## Comments API

- `GET /api/needs/{need}/comments`
- `POST /api/needs/{need}/comments` (auth:sanctum)
- `DELETE /api/comments/{comment}` (auth:sanctum)

## Advanced API

- OCHA Reports: `GET /api/ocha/reports`
- Predictive Analytics: `GET /api/analytics/predictions`
- Donation Chain Verification: `GET /api/donations/verify/{id}`
- Device Push Token: `POST /api/fcm-token` (auth:sanctum)

## Mobile Integration Notes

- Expo app موجود في مجلد `../mobile`.
- أثناء التطوير على Android emulator استخدم `10.0.2.2` بدل `127.0.0.1` للوصول إلى backend.
- عند التشغيل على هاتف فعلي استخدم IP جهازك المحلي.

## Background Jobs, Mail, and Reports

- Queue driver: `database`
- Weekly report command: `php artisan report:weekly`
- Scheduled weekly report: كل يوم اثنين 08:00 (انظر `routes/console.php`)

## Multi-language and Multi-tenancy

- Translatable fields (Spatie):
	- `areas.name`, `areas.description`
	- `needs.notes`
- Organization isolation عبر `OrganizationScope` وعمود `organization_id`.

## Push Notifications (FCM)

اضبط في `.env`:

- `FIREBASE_PROJECT_ID`
- `FIREBASE_SERVER_KEY`

الخدمة تدعم نمطي FCM:

- Legacy server key endpoint
- HTTP v1 endpoint (bearer token)
