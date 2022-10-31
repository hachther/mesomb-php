# PHP MeSomb

## Table of Contents

| Method | Description |
|--------|-------------|
| [**Application**](#Application) |  |
| [Application::__construct](#Application__construct) |  |
| [Application::getKey](#ApplicationgetKey) |  |
| [Application::getLogo](#ApplicationgetLogo) |  |
| [Application::getBalances](#ApplicationgetBalances) |  |
| [Application::getCountries](#ApplicationgetCountries) |  |
| [Application::getDescription](#ApplicationgetDescription) |  |
| [Application::isLive](#ApplicationisLive) |  |
| [Application::getName](#ApplicationgetName) |  |
| [Application::getSecurity](#ApplicationgetSecurity) |  |
| [Application::getStatus](#ApplicationgetStatus) |  |
| [Application::getUrl](#ApplicationgetUrl) |  |
| [Application::getSecurityField](#ApplicationgetSecurityField) | Get a specific setting value |
| [Application::getBalance](#ApplicationgetBalance) | Get current balance |
| [**InvalidClientRequestException**](#InvalidClientRequestException) |  |
| [InvalidClientRequestException::__construct](#InvalidClientRequestException__construct) |  |
| [**PaymentOperation**](#PaymentOperation) | Containing all operations provided by MeSomb Payment Service. |
| [PaymentOperation::__construct](#PaymentOperation__construct) |  |
| [PaymentOperation::makeCollect](#PaymentOperationmakeCollect) | Collect money from a mobile account |
| [PaymentOperation::makeDeposit](#PaymentOperationmakeDeposit) | Make a deposit in a receiver mobile account. |
| [PaymentOperation::updateSecurity](#PaymentOperationupdateSecurity) | Update security settings of your service on MeSomb |
| [PaymentOperation::getStatus](#PaymentOperationgetStatus) | Get the current status of your service on MeSomb |
| [PaymentOperation::getTransactions](#PaymentOperationgetTransactions) | Get transactions from MeSomb by IDs. |
| [**PermissionDeniedException**](#PermissionDeniedException) |  |
| [**ServerException**](#ServerException) |  |
| [ServerException::__construct](#ServerException__construct) |  |
| [**ServiceNotFoundException**](#ServiceNotFoundException) |  |
| [**Settings**](#Settings) |  |
| [**Signature**](#Signature) |  |
| [Signature::signRequest](#SignaturesignRequest) |  |
| [Signature::nonceGenerator](#SignaturenonceGenerator) | Generate a random string by the length |
| [**TransactionResponse**](#TransactionResponse) |  |
| [TransactionResponse::__construct](#TransactionResponse__construct) |  |
| [TransactionResponse::isOperationSuccess](#TransactionResponseisOperationSuccess) |  |
| [TransactionResponse::isTransactionSuccess](#TransactionResponseisTransactionSuccess) |  |
| [TransactionResponse::getSuccess](#TransactionResponsegetSuccess) |  |
| [TransactionResponse::getMessage](#TransactionResponsegetMessage) |  |
| [TransactionResponse::getRedirect](#TransactionResponsegetRedirect) |  |
| [TransactionResponse::getData](#TransactionResponsegetData) |  |
| [TransactionResponse::getReference](#TransactionResponsegetReference) |  |
| [TransactionResponse::getStatus](#TransactionResponsegetStatus) |  |

## Application





* Full name: \MeSomb\Model\Application


### Application::__construct



```php
Application::__construct( mixed data ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **mixed** |  |


**Return Value:**





---
### Application::getKey



```php
Application::getKey(  ): string
```





**Return Value:**





---
### Application::getLogo



```php
Application::getLogo(  ): string
```





**Return Value:**





---
### Application::getBalances



```php
Application::getBalances(  ): array
```





**Return Value:**





---
### Application::getCountries



```php
Application::getCountries(  ): array
```





**Return Value:**





---
### Application::getDescription



```php
Application::getDescription(  ): string
```





**Return Value:**





---
### Application::isLive



```php
Application::isLive(  ): bool
```





**Return Value:**





---
### Application::getName



```php
Application::getName(  ): string
```





**Return Value:**





---
### Application::getSecurity



```php
Application::getSecurity(  ): array
```





**Return Value:**





---
### Application::getStatus



```php
Application::getStatus(  ): string
```





**Return Value:**





---
### Application::getUrl



```php
Application::getUrl(  ): string
```





**Return Value:**





---
### Application::getSecurityField

Get a specific setting value

```php
Application::getSecurityField( string field ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `field` | **string** |  |


**Return Value:**





---
### Application::getBalance

Get current balance

```php
Application::getBalance( string|null country = null, string|null service = null ): int
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `country` | **string\|null** |  |
| `service` | **string\|null** |  |


**Return Value:**





---
## InvalidClientRequestException





* Full name: \MeSomb\Exception\InvalidClientRequestException
* Parent class: 


### InvalidClientRequestException::__construct



```php
InvalidClientRequestException::__construct( string message = "", string code = "", ?\Throwable previous = null ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `message` | **string** |  |
| `code` | **string** |  |
| `previous` | **?\Throwable** |  |


**Return Value:**





---
## PaymentOperation

Containing all operations provided by MeSomb Payment Service.

[Check the documentation here](https://mesomb.hachther.com/en/api/v1.1/schema/)

* Full name: \MeSomb\Operation\PaymentOperation


### PaymentOperation::__construct



```php
PaymentOperation::__construct( string applicationKey, string accessKey, string secretKey ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `applicationKey` | **string** |  |
| `accessKey` | **string** |  |
| `secretKey` | **string** |  |


**Return Value:**





---
### PaymentOperation::makeCollect

Collect money from a mobile account

```php
PaymentOperation::makeCollect( int amount, string service, string payer, \DateTime date, string nonce, string|null trxID = null, string country = 'CM', string currency = 'XAF', bool feesIncluded = true, string mode = 'synchronous', bool conversion = false, array&lt;string,string&gt;|null location = null, array&lt;string,string&gt;|null customer = null, array&lt;string,string&gt;[]|null products = null, array|null extra = null ): \MeSomb\Model\TransactionResponse|void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `amount` | **int** | amount to collect |
| `service` | **string** | MTN, ORANGE, AIRTEL |
| `payer` | **string** | account number to collect from |
| `date` | **\DateTime** | date of the request |
| `nonce` | **string** | unique string on each request |
| `trxID` | **string\|null** | unique string in your local system |
| `country` | **string** | country CM, NE |
| `currency` | **string** | code of the currency of the amount |
| `feesIncluded` | **bool** | if your want MeSomb to include and compute fees in the amount to collect |
| `mode` | **string** | asynchronous or synchronous |
| `conversion` | **bool** | In case of foreign currently defined if you want to rely on MeSomb to convert the amount in the local currency |
| `location` | **array&lt;string,string&gt;\|null** | array-key containing the location of the customer ({town: string, region: string, country: string}) check the documentation. |
| `customer` | **array&lt;string,string&gt;\|null** | array-key containing information of the customer ({email: string, phone: string, town: string, region: string, country: string, first_name: string, last_name: string, address: string}) check the documentation |
| `products` | **array&lt;string,string&gt;[]\|null** | array of product contained in the transaction. Product in this format array{id: string, name: string, category: ?string, quantity: int, amount: float} |
| `extra` | **array\|null** | Extra parameter to send in the body check the API documentation |


**Return Value:**





---
### PaymentOperation::makeDeposit

Make a deposit in a receiver mobile account.

```php
PaymentOperation::makeDeposit( int amount, string service, string receiver, \DateTime date, string nonce, string trxID = null, string country = 'CM', string currency = 'XAF', array|null extra = null ): \MeSomb\Model\TransactionResponse|void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `amount` | **int** | the amount of the transaction |
| `service` | **string** | service code (MTN, ORANGE, AIRTEL, ...) |
| `receiver` | **string** | receiver account (in the local phone number) |
| `date` | **\DateTime** | date of the request |
| `nonce` | **string** | Unique key generated for each transaction |
| `trxID` | **string** | ID of the transaction in your local system |
| `country` | **string** | country code &#039;CM&#039; by default |
| `currency` | **string** | currency of the transaction (XAF, XOF, ...) XAF by default |
| `extra` | **array\|null** | Extra parameter to send in the body check the API documentation |


**Return Value:**





---
### PaymentOperation::updateSecurity

Update security settings of your service on MeSomb

```php
PaymentOperation::updateSecurity( string field, string action, mixed|null value = null, \DateTime|null date = null ): \MeSomb\Model\Application|void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `field` | **string** | which security field you want to update (check API documentation) |
| `action` | **string** | SET or UNSET |
| `value` | **mixed\|null** | value of the field |
| `date` | **\DateTime\|null** | date of the request |


**Return Value:**





---
### PaymentOperation::getStatus

Get the current status of your service on MeSomb

```php
PaymentOperation::getStatus( \DateTime|null date = null ): \MeSomb\Model\Application|void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `date` | **\DateTime\|null** | date of the request |


**Return Value:**





---
### PaymentOperation::getTransactions

Get transactions from MeSomb by IDs.

```php
PaymentOperation::getTransactions( array ids, \DateTime|null date = null ): mixed|void
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `ids` | **array** | list of ids |
| `date` | **\DateTime\|null** | date of the request |


**Return Value:**





---
## PermissionDeniedException





* Full name: \MeSomb\Exception\PermissionDeniedException
* Parent class: 


## ServerException





* Full name: \MeSomb\Exception\ServerException
* Parent class: 


### ServerException::__construct



```php
ServerException::__construct( string message = "", string code = "", ?\Throwable previous = null ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `message` | **string** |  |
| `code` | **string** |  |
| `previous` | **?\Throwable** |  |


**Return Value:**





---
## ServiceNotFoundException





* Full name: \MeSomb\Exception\ServiceNotFoundException
* Parent class: 


## Settings





* Full name: \MeSomb\Settings


## Signature





* Full name: \MeSomb\Signature


### Signature::signRequest



```php
Signature::signRequest( string service, string method, string url, \DateTime date, string nonce, array credentials, array headers = [], array|null body = null ): string
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `service` | **string** | service to use can be payment, wallet ... (the list is provide by MeSomb) |
| `method` | **string** | HTTP method (GET, POST, PUT, PATCH, DELETE...) |
| `url` | **string** | the full url of the request with query element https://mesomb.hachther.com/path/to/ressource?highlight=params#url-parsing |
| `date` | **\DateTime** | Datetime of the request |
| `nonce` | **string** | Unique string generated for each request sent to MeSomb |
| `credentials` | **array** | dict containing key =&gt; value for the credential provided by MeSOmb. {&#039;access&#039; =&gt; access_key, &#039;secret&#039; =&gt; secret_key} |
| `headers` | **array** | Extra HTTP header to use in the signature |
| `body` | **array\|null** | The dict containing the body you send in your request body |


**Return Value:**

Authorization to put in the header



---
### Signature::nonceGenerator

Generate a random string by the length

```php
Signature::nonceGenerator( int length = 40 ): string
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `length` | **int** |  |


**Return Value:**





---
## TransactionResponse





* Full name: \MeSomb\Model\TransactionResponse


### TransactionResponse::__construct



```php
TransactionResponse::__construct( mixed data ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `data` | **mixed** |  |


**Return Value:**





---
### TransactionResponse::isOperationSuccess



```php
TransactionResponse::isOperationSuccess(  ): mixed
```





**Return Value:**





---
### TransactionResponse::isTransactionSuccess



```php
TransactionResponse::isTransactionSuccess(  ): mixed
```





**Return Value:**





---
### TransactionResponse::getSuccess



```php
TransactionResponse::getSuccess(  ): bool|mixed
```





**Return Value:**





---
### TransactionResponse::getMessage



```php
TransactionResponse::getMessage(  ): mixed|string
```





**Return Value:**





---
### TransactionResponse::getRedirect



```php
TransactionResponse::getRedirect(  ): mixed|string
```





**Return Value:**





---
### TransactionResponse::getData



```php
TransactionResponse::getData(  ): array|mixed
```





**Return Value:**





---
### TransactionResponse::getReference



```php
TransactionResponse::getReference(  ): mixed|string
```





**Return Value:**





---
### TransactionResponse::getStatus



```php
TransactionResponse::getStatus(  ): mixed|string
```





**Return Value:**





---
