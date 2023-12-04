Demo Application for the chunked Turbo Streams blogpost.

### Running

This application uses Docker and Docker Compose (it's not on Sail because we need multiple PHP processes). To run it locally, try:

```bash
cp .env.example .env
docker compose up -d
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan storage:link
docker compose exec laravel.test php artisan migrate
docker compoes exec laravel.test php artisan tailwindcss:download
docker compoes exec laravel.test php artisan tailwindcss:build
```
