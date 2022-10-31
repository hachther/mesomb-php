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

### 🏠 [Homepage](https://mesomb.com)

## Install

```sh
composer require hachther/mesomb
```

## Documentation for endpoints

### Payment

All API related to the payment service available in ```MeSomb\Operation\PaymentOperation```

| Method                                                                                        | Endpoint              | Description                                        |
|-----------------------------------------------------------------------------------------------|-----------------------|----------------------------------------------------|
| [makeCollect](docs/classes/MeSomb-Operation-PaymentOperation.html#method_makeCollect)         | payment/collect/      | Collect money from a mobile account                |
| [makeDeposit](docs/classes/MeSomb-Operation-PaymentOperation.html#method_makeDeposit)         | payment/deposit/      | Make a deposit in a receiver mobile account        |
| [updateSecurity](docs/classes/MeSomb-Operation-PaymentOperation.html#method_updateSecurity)   | payment/security/     | Update security settings of your service on MeSomb |
| [getStatus](docs/classes/MeSomb-Operation-PaymentOperation.html#method_getStatus)             | payment/status/       | Get the current status of your service on MeSomb   |
| [getTransactions](docs/classes/MeSomb-Operation-PaymentOperation.html#method_getTransactions) | payment/transactions/ | Get transactions from MeSomb by IDs.               |


## Usage

### Collect money from an account

```PHP
<?php
use MeSomb\Operation\PaymentOperation;
use MeSomb\Signature;

$client = new PaymentOperation('<applicationKey>', '<AccessKey>', '<SecretKey>');
$client->makeCollect(100, 'MTN', '670000000', new DateTime(), Signature::nonceGenerator());
```

### Depose money in an account

```PHP
<?php
use MeSomb\Operation\PaymentOperation;
use MeSomb\Signature;

$client = new PaymentOperation('<applicationKey>', '<AccessKey>', '<SecretKey>');
$client->makeDeposit(100, 'MTN', '670000000', new DateTime(), Signature::nonceGenerator());
```

### Get application status

```PHP
<?php
use MeSomb\Operation\PaymentOperation;
use MeSomb\Signature;

$client = new PaymentOperation('<applicationKey>', '<AccessKey>', '<SecretKey>');
$application = $client->getStatus();
print_r($application->getStatus());
print_r($application->getBalance());
```

### Get transactions by ids

```PHP
<?php
use MeSomb\Operation\PaymentOperation;
use MeSomb\Signature;

$client = new PaymentOperation('<applicationKey>', '<AccessKey>', '<SecretKey>');
$transactions = $client->getTransactions(['ID1', 'ID2']);
print_r($transactions);
```

## Author

👤 **Hachther LLC <contact@hachther.com>**

* Website: https://www.hachther.com
* Twitter: [@hachther](https://twitter.com/hachther)
* Github: [@hachther](https://github.com/hachther)
* LinkedIn: [@hachther](https://linkedin.com/in/hachther)

## Show your support

Give a ⭐️ if this project helped you!
