# Laravel E-Commerce 1

A modern, feature-rich e-commerce platform built with the latest version of the
Laravel ecosystem. It ships with a complete storefront, an admin panel powered
by Filament, blazing-fast full-text search powered by Typesense, and a
role-based permission system.

> The storefront UI is in **Turkish**, but the codebase and this documentation
> are in English.

## ✨ Features

**Storefront**
- Product catalog with categories, brands, campaigns and tags
- Product detail pages with image galleries, attributes and ratings
- Cart, wishlist (favorites) and checkout with coupons and shipment discounts
- Product comments & ratings, FAQ, contact and informational pages
- Fast, live full-text search (Typesense + Laravel Scout)
- Responsive design with **Tailwind CSS v4** and **daisyUI v5**
- Livewire 4 components and Vue 3 search widgets

**Admin panel (Filament 5)**
- Full CRUD for products, categories, brands, campaigns, orders, coupons,
  shipments, users, comments, testimonials, sliders and site settings
- Dashboard with analytics widgets
- Role & permission management (spatie/laravel-permission)

**Access control**
- Only users with the **`admin`** role can access `/admin` — everyone else is
  rejected with `403`
- Separate storefront login/register for customers (the `user` role)
- API routes guarded with fine-grained `can:` permission middleware

**Integrations**
- **Typesense** search engine (Docker image included)
- Social login: Google, Facebook, GitHub, Instagram (Laravel Socialite)
- Payments: iyzico and Stripe, invoice generation (laravel-invoices)
- OpenAI & Hugging Face API clients for AI-assisted features
- Laravel Sanctum API tokens for mobile clients

## 🧰 Tech Stack

| Layer      | Technology                                              |
| ---------- | ------------------------------------------------------- |
| Backend    | PHP 8.4, Laravel 13, Livewire 4                         |
| Admin      | Filament 5                                              |
| Frontend   | Blade, Vue 3, Tailwind CSS 4, daisyUI 5, Vite 8         |
| Database   | PostgreSQL 18                                           |
| Search     | Typesense 30 (Laravel Scout)                            |
| Auth/RBAC  | Laravel Socialite, Sanctum, spatie/laravel-permission   |
| Testing    | Pest 5                                                  |
| Tooling    | Docker / Laravel Sail / Podman, Laravel Pint            |

## 📋 Requirements

- PHP **8.4+** with `intl`, `exif`, `iconv`, `gd` and `pdo_pgsql` extensions
- Composer **2.7+**
- Node.js **22+** and npm
- Docker (or Podman) — optional but recommended
- PostgreSQL — or use the bundled Docker service

## 🚀 Quick Start (Docker / Laravel Sail)

```bash
# 1. Install dependencies
composer install

# 2. Environment configuration
cp .env.example .env
#   Edit .env and set DB_*, TYPESENSE_* and APP_* values (see below)

# 3. Start the stack (app + PostgreSQL + Typesense)
./vendor/bin/sail up -d --build

# 4. Run migrations and seed the database
./vendor/bin/sail php artisan migrate:fresh --seed

# 5. Install & build frontend assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run build

# 6. Import products into the Typesense search index
./vendor/bin/sail php artisan scout:import "App\Models\Product"

# 7. Open the storefront
open http://localhost:2121
```

### Using Podman instead of Docker

The same `docker-compose.yml` works with Podman:

```bash
podman compose up -d --build
podman compose exec app php artisan migrate:fresh --seed
podman compose exec app npm install
podman compose exec app npm run build
podman compose exec app php artisan scout:import "App\Models\Product"
```

> **Rootless Podman note:** the compose file sets `SUPERVISOR_PHP_USER: root`
> so PHP writes to host-mounted directories with your local user's permissions.

## 💻 Manual Setup (without Docker)

```bash
composer install
cp .env.example .env
php artisan key:generate

# Configure a PostgreSQL database in .env, then:
php artisan migrate --seed
php artisan scout:import "App\Models\Product"

npm install
npm run dev   # or: npm run build
php artisan serve --port=2121
```

## ⚙️ Environment Configuration

Copy `.env.example` to `.env` and adjust at least:

```env
APP_NAME="AKALIN TECH E-COMMERCE"
APP_URL=http://localhost:2121

# PostgreSQL (matches the Docker service)
DB_CONNECTION=pgsql
DB_HOST=pgsql            # 127.0.0.1 when running without Docker
DB_PORT=5432
DB_DATABASE=ecommerce
DB_USERNAME=sail
DB_PASSWORD=password

# Typesense (matches the Docker service)
SCOUT_DRIVER=typesense
TYPESENSE_HOST=typesense # 127.0.0.1 when running without Docker
TYPESENSE_PORT=8108
TYPESENSE_PROTOCOL=http
TYPESENSE_API_KEY=xyz
```

The following integrations are optional and can be left as placeholders:

- `IYZIPAY_API_KEY`, `IYZIPAY_SECRET_KEY` — iyzico payments
- `STRIPE_KEY`, `STRIPE_SECRET` — Stripe payments
- `GOOGLE_CLIENT_ID/SECRET`, `FACEBOOK_*`, `GITHUB_*` — social login
- `OPENAI_API_KEY`, `HUGGINGFACE_API_KEY` — AI features

## 🔐 Demo Accounts

After `migrate:fresh --seed` the following accounts exist:

| Role    | Email              | Password                                     |
| ------- | ------------------ | -------------------------------------------- |
| Admin   | `admin@example.com`| value of `ADMIN_PASSWORD` in your `.env`     |
| Customer| `test@example.com` | value of `TEST_PASSWORD` in your `.env`      |

The admin panel is available at `/admin`.

## 🧪 Testing

```bash
php artisan test
```

The test suite covers the homepage, admin panel access control
(admin-only), and the storefront authentication flow (login, logout,
registration).

## 🗂 Project Structure

```
app/
├── Filament/          # Admin panel resources, pages, widgets
├── Http/Controllers/  # Storefront & API controllers
├── Livewire/          # Livewire 3/4 components
├── Models/            # Eloquent models
├── Providers/         # Service providers & Filament panel provider
├── Services/          # OpenAI, Hugging Face, etc.
└── Policies/          # Authorization policies
database/
├── migrations/
├── factories/
└── seeders/           # Roles, permissions & demo data
resources/
├── views/             # Blade + Livewire views (storefront)
├── css/               # Tailwind CSS 4 + daisyUI theme
└── js/                # Vue 3 components (search, etc.)
routes/
├── web.php            # Storefront routes
└── api.php            # API v1 routes (Sanctum + permission guards)
```

## 🤝 Contributing

Contributions are welcome! Please read
[CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the
process for submitting pull requests.

## 🛡 Security

If you discover a security vulnerability, please follow the steps described in
[SECURITY.md](SECURITY.md) rather than opening a public issue.

## 📄 License

This project is open-sourced under the [MIT license](LICENSE).
