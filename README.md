# Order Management Service

## Setup Instructions

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-repo/order-management.git
   cd order-management
   ```
2. **Create a copy of your .env file and set its variables :**

```bash
   cp .env.example .env
```

3. **Run Docker services :**

```bash
   ./vendor/bin/sail up
```

4. **Generate an app encryption key:**

```bash
   php artisan key:generate
```

5. **In the .env file, add database information to allow Laravel to connect to the database**
6. **Migrate the database**

```bash
   php artisan passport:install
```

7. **Update .env file add passport client keys**

```bash
 eg.
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=YOUR_CLIENT_ID
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=YOUR_CLIENT_SECRET

PASSPORT_PASSWORD_GRANT_CLIENT_ID=YOUR_CLIENT_ID
PASSPORT_PASSWORD_GRANT_CLIENT_SECRET=YOUR_CLIENT_SECRET

```

8. **Seed the database**

```bash
   php artisan db:seed
```

9. **Run tests**

```bash
   php artisan test
```

## How to add new gateway ?

1. **Create a new class in app\Strategies\Gateways folder**
2. **Add the new gateway to app\Strategies\Gateways\PaymentGatewayStrategy gateways array**
3. **Add the new gateway to payment method enum column in the payments table**
4. **Migrate the new schema php artisan migrate**
5. **Edit app/Http/Requests/Api/V1/Payment/CreatePaymentRequest class to add new payment method**
6. **Add the new gateway in App\Services\PaymentGateway\PaymentGatewayFactory payment_method**
