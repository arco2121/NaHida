# Database Setup Guide — NaHida IoT

This guide explains how to set up the database from scratch using Laravel migrations and seeders.

---

## Prerequisites

- XAMPP running with MySQL active
- A database named `nahida_iot` created in phpMyAdmin
- `.env` file configured with your local DB credentials

## First-time setup

Run migrations to create all tables:

```bash
php artisan migrate
```

Then run seeders to populate the database with test data:

```bash
php artisan db:seed
```

That's it. You now have:

- 3 test users
- 1 plant per user
- 5 sensor readings per plant
- 3 watering events per plant

### Test credentials

All users share the same password: `password1234`

| Name | Email |
|---|---|
| Marco Rossi | marco@nahida.it |
| Giulia Bianchi | giulia@nahida.it |
| Luca Verdi | luca@nahida.it |

---

## Resetting the database

If you need to wipe everything and start fresh:

```bash
php artisan migrate:fresh --seed
```

This drops all tables, re-runs all migrations, and re-seeds the database in one command.

---

## Useful commands

| Command | What it does |
|---|---|
| `php artisan migrate` | Run pending migrations |
| `php artisan migrate:status` | Show migration status |
| `php artisan migrate:rollback` | Undo the last batch of migrations |
| `php artisan migrate:fresh` | Drop all tables and re-run migrations |
| `php artisan migrate:fresh --seed` | Fresh migration + seed in one shot |
| `php artisan db:seed` | Run all seeders |

---

## Database schema

### `users`
Stores user accounts.

| Column | Type | Notes |
|---|---|---|
| user_id | bigint PK | Auto-increment |
| email | string | Unique |
| password | string | Hashed with bcrypt |
| first_name | string | |
| last_name | string | |
| created_at / updated_at | timestamp | Managed by Laravel |

### `plants`
One plant per user, stores optimal conditions and Live2D appearance settings.

| Column | Type | Notes |
|---|---|---|
| plant_id | bigint PK | Auto-increment |
| user_id | bigint FK | References users |
| plant_name | string | |
| notes | text | Nullable |
| hum_min / hum_max | float | Humidity range (%) |
| temp_min / temp_max | float | Temperature range (°C) |
| soil_hum_min / soil_hum_max | float | Soil humidity range (%) |
| lum_preference | enum | `low`, `medium`, `high`, `direct` — nullable |
| watering_cycle | integer | Hours between waterings |
| plant_variant | string | Nullable — Live2D model variant |
| plant_color | string | Nullable |
| flower_color | string | Nullable |
| pot_color | string | Nullable |
| created_at / updated_at | timestamp | Managed by Laravel |

### `sensor_readings`
Sensor data published by the ESP32 every minute.

| Column | Type | Notes |
|---|---|---|
| reading_id | bigint PK | Auto-increment |
| plant_id | bigint FK | References plants |
| humidity | float | % |
| temperature | float | °C |
| soil_humidity | float | % |
| luminosity | float | Lux — nullable (sensor may not be present) |
| recorded_at | timestamp | When the reading was taken |

### `watering_events`
Logged every time the plant is watered.

| Column | Type | Notes |
|---|---|---|
| watering_id | bigint PK | Auto-increment |
| plant_id | bigint FK | References plants |
| watered_at | timestamp | When the watering happened |
| source | enum | `button`, `manual_app`, `scheduled` |
