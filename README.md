# mesomb-php-client
PHP client for MeSomb payment system.

Beform you can use this you must register on MeSomb [here](https://mesomb.hachther.com/en/signup) and create an application to obtain your application key.

## Sample code

```PHP
<?php

require('vendor/autoload.php');

use Hachther\MeSomb\Client;

$client = new Client('-----Application-Key------');
$client->makePayment('237670707070', 25, 'MTN');
```
