<h1 align="center">Welcome to php-mesomb 👋</h1>
<p>
  <img alt="Version" src="https://img.shields.io/badge/version-1.2-blue.svg?cacheSeconds=2592000" />
  <a href="https://mesomb.hachther.com/en/api/v1.1/schema/" target="_blank">
    <img alt="Documentation" src="https://img.shields.io/badge/documentation-yes-brightgreen.svg" />
  </a>
  <a href="#" target="_blank">
    <img alt="License: MIT" src="https://img.shields.io/badge/License-MIT-yellow.svg" />
  </a>
  <a href="https://twitter.com/hachther" target="_blank">
    <img alt="Twitter: hachther" src="https://img.shields.io/twitter/follow/hachther.svg?style=social" />
  </a>
</p>

> PHP client for MeSomb services.
> 
> You can check the full [documentation of the api here](https://mesomb.hachther.com/en/api/v1.1/schema/)

## 🏠 Requirements

PHP 5.6.0 and later.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```sh
composer require hachther/mesomb-php
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once 'vendor/autoload.php';
```

## Manual Installation

If you do not wish to use Composer, you can download the latest release. Then, to use the bindings, include the init.php file.

```php
require_once '/path/to/mesomb-php/init.php';
```

## Dependencies

The bindings require the following extensions in order to work properly:

- [curl](https://secure.php.net/manual/en/book.curl.php), although you can use your own non-cURL client if you prefer
- [json](https://secure.php.net/manual/en/book.json.php)
- [mbstring](https://secure.php.net/manual/en/book.mbstring.php) (Multibyte String)

If you use Composer, these dependencies should be handled automatically. If you install manually, you'll want to make sure that these extensions are available.

## Getting Stated

### Collect money from an account

```PHP
<?php
use MeSomb\Operation\PaymentOperation;

$applicationKey = 'application key';
$accessKey = 'access key';
$secretKey = 'secret key';
$client = new PaymentOperation($applicationKey, $accessKey, $secretKey);

$response = $client->makeCollect([
    'payer' => '670000000',
    'amount' => 10000,
    'service' => 'MTN',
    'country' => 'CM',
    'currency' => 'XAF',
    'customer' => [
        'email' => 'email@gmail.com',
        'first_name' => 'Dan',
        'last_name' => 'Fisher',
        'town' => 'Douala',
        'region' => 'Littoral',
        'country' => 'CM',
        'address' => 'Bepanda',
    ],
    'products' => [
        [
            'id' => 'SKU001',
            'name' => 'Sac a Dos',
            'category' => 'Sac',
            'quantity' => 1,
            'amount' => 10000
        ]
    ],
    'location' => [
        'town' => 'Douala',
        'region' => 'Littoral',
        'country' => 'CM'
    ]
]);
$response->isOperationSuccess();
$response->isTransactionSuccess();
```

### Depose money in an account

```PHP
<?php
use MeSomb\Operation\PaymentOperation;

$applicationKey = 'application key';
$accessKey = 'access key';
$secretKey = 'secret key';
$client = new PaymentOperation($applicationKey, $accessKey, $secretKey);

$response = $client->makeDeposit([
    'receiver' => '670000000',
    'amount' => 10000,
    'service' => 'MTN',
    'country' => 'CM',
    'currency' => 'XAF',
    'customer' => [
        'email' => 'email@gmail.com',
        'first_name' => 'Dan',
        'last_name' => 'Fisher',
        'town' => 'Douala',
        'region' => 'Littoral',
        'country' => 'CM',
        'address' => 'Bepanda',
    ],
    'products' => [
        [
            'id' => 'SKU001',
            'name' => 'Sac a Dos',
            'category' => 'Sac',
            'quantity' => 1,
            'amount' => 10000
        ]
    ],
    'location' => [
        'town' => 'Douala',
        'region' => 'Littoral',
        'country' => 'CM'
    ]
]);
$response->isOperationSuccess();
$response->isTransactionSuccess();
```

### Get application status

```PHP
<?php
use MeSomb\Operation\PaymentOperation;
use MeSomb\Util\RandomGenerator;

$client = new PaymentOperation('<applicationKey>', '<AccessKey>', '<SecretKey>');
$application = $client->getStatus();
print_r($application->getStatus());
print_r($application->getBalance());
```

### Get transactions by ids

```PHP
<?php
use MeSomb\Operation\PaymentOperation;

$applicationKey = 'application key';
$accessKey = 'access key';
$secretKey = 'secret key';
$client = new PaymentOperation($applicationKey, $accessKey, $secretKey);
$response = $client->getTransactions(['a483a9e8-51d7-44c9-875b-1305b1801274']);
print_r($response);
```

## Documentation

### Payment

All API related to the payment service available in ```MeSomb\Operation\PaymentOperation```

| Method                                                            | Endpoint              | Description                                        |
|-------------------------------------------------------------------|-----------------------|----------------------------------------------------|
| [makeCollect](docs/README.md#PaymentOperationmakeCollect)         | payment/collect/      | Collect money from a mobile account                |
| [makeDeposit](docs/README.md#PaymentOperationmakeDeposit)         | payment/deposit/      | Make a deposit in a receiver mobile account        |
| [updateSecurity](docs/README.md#PaymentOperationupdateSecurity)   | payment/security/     | Update security settings of your service on MeSomb |
| [getStatus](docs/README.md#PaymentOperationgetStatus)             | payment/status/       | Get the current status of your service on MeSomb   |
| [getTransactions](docs/README.md#PaymentOperationgetTransactions) | payment/transactions/ | Get transactions from MeSomb by IDs.               |

## Author

👤 **Hachther LLC <contact@hachther.com>**

* Website: https://www.hachther.com
* Twitter: [@hachther](https://twitter.com/hachther)
* Github: [@hachther](https://github.com/hachther)
* LinkedIn: [@hachther](https://linkedin.com/in/hachther)

## Show your support

Give a ⭐️ if this project helped you!
