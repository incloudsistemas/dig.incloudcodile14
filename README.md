<p align="center"><a href="https://incloudsistemas.com.br" target="_blank"><img src="https://github.com/incloudsistemas/dig.incloudcodile14/blob/main/public/images/filament/i2c-loader.png" alt="The i2C | Marketing Automation, CMS, CRM and Finance logo."></a></p>

## InCloudCodile14 - i2C | Marketing Automation, CMS, CRM and Finance.

InCloudCodile14 - i2C is a powerful solution encompassing Marketing Automation, CMS, CRM, and Finance modules. Built atop the <a href="https://laravel.com/" target="_blank">Laravel framework</a> and <a href="https://tallstack.dev/" target="_blank">TALL Stack</a> using <a href="https://filamentphp.com/" target="_blank">Filament V.3</a>. It offers a comprehensive solution for businesses looking to optimize their processes and achieve their business goals more efficiently.

## Requirements

- PHP 8.1+
- MySQL 8+

## Installation

Clone the repository and cd into it:

```bash
git clone git@github.com:incloudsistemas/dig.incloudcodile14.git

cd incloudcodile14
```

Create a `.env` file and generate the app key:

```bash
cp .env.example .env

php artisan key:generate
```

Install the dependencies:

```bash
composer install
```

Create the database:

```bash
mysql -u root -e "CREATE DATABASE incloudcodile14"
```

Migrate the database with some fake data:

```bash
php artisan migrate --seed
```

Install the frontend dependencies and build the assets:

```bash
npm instal && npm run dev
```

## Testing

To run the tests, execute the following command:

```bash
php artisan test
```

## Security Vulnerabilities

If you discover a security vulnerability within InCloudCodile14, please send an e-mail to Vinícius C. Lemos via [contato@incloudsistemas.com.br](mailto:contato@incloudsistemas.com.br). All security vulnerabilities will be promptly addressed.

## License

The InCloudCodile14 is open-sourced software.