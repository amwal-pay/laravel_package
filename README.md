# AmwalPay Laravel Package

## Installation
1. Install the AmwalPay Laravel Package via [amwalpay/laravel-package](https://packagist.org/packages/amwalpay/laravel-package)

```bash
composer require amwalpay/laravel-package:dev-main
```

2. Publish the AmwalPay Service Provider using the following command.

```bash
php artisan vendor:publish --provider="AmwalPay\LaravelPackage\AmwalPayServiceProvider" --tag="amwalpay"
```

3. Customize the process and callback actions that exist in **app/Http/Controllers/AmwalPayController.php** file as per your needs.

## Configuration
### AmwalPay Account
1. Sign up at [AmwalPay](https://www.amwal-pay.com/) and and our sales team will reach out to you. Once contract is signed, we’ll send you your Merchant ID, Terminal ID, and Secure Key. 

### Merchant Configurations
1. Edit the **config/amwalpay.php** file and paste each key in its place.
2. replace only the {YourWebsiteURL} with your site domain.

> https://{YourWebsiteURL}/amwalpay/callback

3. Below URL is considered as your website payment process for AmwalPay Payment. Just replace the `{YourWebsiteURL}` with the actual domain.

> https://{YourWebsiteURL}/amwalpay/process
