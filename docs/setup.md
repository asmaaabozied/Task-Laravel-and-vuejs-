# Setup Instructions

1. Copy the environment file:

```bash
cp .env.example .env
```

2. Set database and Slack webhook configuration in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_k8s
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Laravel"

SLACK_WEBHOOK_URL=https://hooks.slack.com/services/...
```

3. Install PHP dependencies:

```bash
composer install
```

4. Install frontend dependencies:

```bash
npm install
```

5. Run migrations:

```bash
php artisan migrate
```

6. Build assets or start the development server:

```bash
npm run dev
```

7. Start queue workers for notification retries:

```bash
php artisan queue:work
```

8. Access the escalation page in the browser:

```text
http://localhost/tickets/{id}/escalate
http://127.0.0.1:8000/tickets/{id}/escalate
```

9. Use the API endpoint to escalate a ticket:

```http
POST /api/tickets/{id}/escalate
Content-Type: application/json
{
  "channels": ["email", "slack"]
}
```
