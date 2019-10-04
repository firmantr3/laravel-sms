# Laravel Sms

Third-party HTTP SMS Gateway library for Laravel. It supports zenziva, nusasms, smsgateway[dot]me by default. But you can extend it by just updating the config file.

## Installation

```bash
composer require firmantr3/laravel-sms
```

## Configuration

### Register Service Provider (Laravel <= 5.4)

Add to your

```php
<?php
return [
    'providers': [
        // Other Providers above
        Firmantr3\Sms\SmsServiceProvider::class,
    ],
];
```

### Publish Config

```bash
php artisan vendor:publish --provider=Firmantr3\\Sms\\SmsServiceProvider
```

### Update Config

Update your preferences in `config/sms.php`.

## Usage

### Sending Sms

```php
<?php

use Firmantr3\Sms\Facade\Sms;

// send using default sms channel
Sms::text('test')->phone('081138702880')->send();

// send using particular sms channel
$sms = Sms::channel('zenziva')->text('test text zenziva')->phone('081138702880');
$sms->send();

// debug response data
print_r($sms->getResponse());

```

## Test

```bash
vendor/bin/phpunit
```
