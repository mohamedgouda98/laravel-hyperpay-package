
# HyperPay Package With Laravel

sample package for laravel applications to integrate with HyperPay

# Hi, I'm Gouda! 👋


## 🚀 About Me
I'm a Tecnical team lead...


## Installation

Install with composer

```bash
  composer require gouda/laravel-hyperpay
```

Add service Providor in config/app.php

```bash
\Gouda\LaravelHyperpay\HyperPayServiceProvider::class,
```

run migration
```bash
php artisan migrate
```



    
## Environment Variables

To run this package, you will need to add the following environment variables to your .env file

`HYPERPAY_TOKEN`

`HYPERPAY_ENTITY_ID`

`HYPERPAY_URL`

OR run command:
```bash
 php artisan vendor:publish --tag=HyperPay-package-config
```
## How to use ?

### payment without user:
- this function to make transaction without Model User , just pass following params:

```bash
$payment = new HyperPayPayment();
$payment->payWithOutUser('mohamed', 1000, '1311 1111 111 111', '05/25', '123');
// params : [name , amount, card number , card expirationdate, card security code]
```

This function will return payment number and save log in database.

## Support

For support, email dev.mohamedgouda@gmail.com 

