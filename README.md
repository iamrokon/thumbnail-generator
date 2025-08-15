# Bulk Image Processing App

A Laravel-based application that allows users to submit multiple image URLs (bulk requests) for processing.  
Processing is handled asynchronously via Laravel Queues, and can be integrated with an external Node.js service for thumbnail generation or other image transformations.

## Features

- **Tier-based limits**: Different tiers (e.g., `basic`, `pro`, `enterprise`) with configurable URL limits and queue priorities.
- **Bulk request creation**: Submit multiple image URLs in one request.
- **Queue processing**: Each image is processed in the background via Laravel jobs.
- **Node.js integration**: Easily swap out the image processing backend by implementing a service interface.
- **Real-time updates**: Broadcast events when images are processed, so the frontend can show progress.
- **Inertia.js frontend**: Simple and interactive UI for managing requests.
- **REST API support**: All endpoints return JSON if requested.

---

## Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Vue 3 + Inertia.js
- **Queue**: Laravel Queue (database)
- **Database**: MySQL
- **Broadcasting**: Laravel Echo + Pusher
- **External Processing**: Optional Node.js microservice

---

## Installation

### 1. Clone the repository
```bash
git clone [https://github.com/yourusername/bulk-image-processing.git](https://github.com/iamrokon/thumbnail-generator.git)
cd bulk-image-processing
```

### 2. Install dependencies
```bash
composer install
npm install
```

### 3. Set up environment
```bash
cp .env.example .env
php artisan key:generate
```

Configure your `.env`:
```env
DB_CONNECTION=mysql

BROADCAST_DRIVER=pusher
QUEUE_CONNECTION=database

PUSHER_APP_ID=780350
PUSHER_APP_KEY=98786b526d0624ead656
PUSHER_APP_SECRET=c3d995c1ed4439b676f0
PUSHER_APP_CLUSTER=mt1
PUSHER_SCHEME=https

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_FORCE_TLS=true
```

---

## Configuration

### Tier Settings
Tier limits and queues are defined in `config/tiers.php`:
```php
return [
    'basic' => ['limit' => 50, 'queue' => 'free'],
    'pro' => ['limit' => 100, 'queue' => 'pro'],
    'enterprise' => ['limit' => 200, 'queue' => 'enterprise'],
];
```

---

## Database & Seeding

```bash
php artisan migrate --seed
```
This will:
- Create all tables
- Seed random users for each tier
- Seed **three fixed test accounts**:
  - `basic@example.com` / `password`
  - `pro@example.com` / `password`
  - `enterprise@example.com` / `password`

---

## Running the App

### Start the queue worker
```bash
php artisan queue:work --queue=enterprise,pro,free,default
```

### Start the dev server
```bash
php artisan serve
npm run dev
```

---

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/bulk-requests` | List bulk requests |
| POST | `/bulk-requests` | Create new bulk request |
| GET | `/bulk-requests?status=processed` | Filter by status |

---

## Event Broadcasting

When an image is processed, the app broadcasts:
```php
BulkRequestUpdated
```
Payload:
```json
{
    "bulk_request_id": 1,
    "status": "processed"
}
```
Frontend can listen via Laravel Echo for real-time updates.

---

## Swapping Processing Backend

All image processing is abstracted via the `ImageProcessorInterface`.  
By default, `NodeImageProcessorService` is used.  
To switch to another system (e.g., Python microservice), create a new class implementing the interface and bind it in a service provider:

```php
$this->app->bind(ImageProcessorInterface::class, MyPythonProcessor::class);
```

---

## License
MIT License.
