
## Setup
**You will need to be running at least PHP8.1**

Run the following commands to get setup

```shell
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Set `DB_DATABASE` in .env to your storage folder for your application location

```shell
php artisan migrate
php artisan db:seed
```

There will now be a default user of `admin@example.com` and password is `password`

The application currently has one implementation for the `CurrencyConverter` interface which is Currency Layer. You will need to get an API key from https://currencylayer.com/quickstart and set `CURRENCY_LAYER_API_KEY` in your `.env` file with your API key

## Running the application

Run the Vite dev server with the following command, which will allow for hot reload when any changes are made to the frontend code
```shell
npm run dev
```

Run the following to have the backend API running

```shell
php artisan serve
```

The following command will start the queue worker for processing background jobs
```shell
php artisan queue:work
```

You can now load the application in your browser at `http://127.0.0.1:8000`
