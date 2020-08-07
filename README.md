# mesomb-php-client
PHP client for MeSomb payment system.

Beform you can use this you must register on MeSomb [here](https://mesomb.hachther.com/en/signup) and create an application to obtain your application key.

## Sample code

### Make payment

```PHP
<?php

require('vendor/autoload.php');

use Hachther\MeSomb\Client;

$client = new Client('-----Application-Key------');
$client->makePayment('237670707070', 25, 'MTN');
```

### Make deposit

```PHP
<?php

require('vendor/autoload.php');

use Hachther\MeSomb\Client;

$client = new Client('-----Application-Key------');
$client->setPin('----Deposit PIN Code -----');
$client->setApiKey('------ Authentication Key ----');
$client->makeDeposit('237670707070', 25, 'MTN');
```
