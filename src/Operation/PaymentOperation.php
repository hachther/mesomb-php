<?php

namespace MeSomb\Operation;

use DateTime;
use MeSomb\Exception\InvalidClientRequestException;
use MeSomb\Exception\PermissionDeniedException;
use MeSomb\Exception\ServerException;
use MeSomb\Exception\ServiceNotFoundException;
use MeSomb\HttpClient\CurlClient;
use MeSomb\MeSomb;
use MeSomb\Model\Application;
use MeSomb\Model\TransactionResponse;
use MeSomb\Signature;

/**
 * Containing all operations provided by MeSomb Payment Service.
 *
 * [Check the documentation here](https://mesomb.hachther.com/en/api/v1.1/schema/)
 */
class PaymentOperation
{
    /**
     * Your service application key on MeSomb
     *
     * @var string $applicationKey
     */
    private $applicationKey;

    /**
     * Your access key provided by MeSomb
     *
     * @var string $accessKey
     */
    private $accessKey;

    /**
     * Your secret key provided by MeSomb
     *
     * @var string $secretKey
     */
    private $secretKey;

    /**
     * @param string $applicationKey
     * @param string $accessKey
     * @param string $secretKey
     */
    public function __construct($applicationKey, $accessKey, $secretKey)
    {
        $this->applicationKey = $applicationKey;
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    private function buildUrl($endpoint) {
        $host = MeSomb::$apiBase;
        $apiVersion = MeSomb::$apiVersion;
        return "$host/en/api/$apiVersion/$endpoint";
    }

    /**
     * @param $method
     * @param $endpoint
     * @param $date
     * @param $nonce
     * @param array $headers
     * @param array|null $body
     * @return string
     */
    private function getAuthorization($method, $endpoint, $date, $nonce, array $headers = [], array $body = null)
    {
        $url = $this->buildUrl($endpoint);

        $credentials = ['accessKey' => $this->accessKey, 'secretKey' => $this->secretKey];

        return Signature::signRequest('payment', $method, $url, $date, $nonce, $credentials, $headers, $body);
    }

    /**
     * @param int $statusCode HTTP Error code
     * @param string $response text body content for curl reponse
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException
     * @throws PermissionDeniedException
     */
    private function processClientException($statusCode, $response) {
        $code = null;
        $message = $response;
        if (strpos($response, "{") == 0) {
            $data = json_decode($response, true);
            $message = $data['detail'];
            $code = $data['code'];
        }
        switch ($statusCode) {
            case 404:
                throw new ServiceNotFoundException($message);
            case 403:
            case 401:
                throw new PermissionDeniedException($message);
            case 400:
                throw new InvalidClientRequestException($message, $code);
            default:
                throw new ServerException($message, $code);
        }
    }

    /**
     * Collect money from a mobile account
     *
     * @param int $amount amount to collect
     * @param string $service MTN, ORANGE, AIRTEL
     * @param string $payer account number to collect from
     * @param DateTime $date date of the request
     * @param string $nonce unique string on each request
     * @param string|null $trxID unique string in your local system
     * @param string|null $country country CM, NE
     * @param string|null $currency code of the currency of the amount
     * @param bool|null $feesIncluded if you want MeSomb to deduct he fees in the collected amount
     * @param string|null $mode asynchronous or synchronous
     * @param bool|null $conversion In case of foreign currently defined if you want to rely on MeSomb to convert the amount in the local currency
     * @param array<string, string>|null $location array-key containing the location of the customer ({town: string, region: string, country: string}) check the documentation.
     * @param array<string, string>|null $customer array-key containing information of the customer ({email: string, phone: string, town: string, region: string, country: string, first_name: string, last_name: string, address: string}) check the documentation
     * @param array<array<string, string>>|null $products array of product contained in the transaction. Product in this format array{id: string, name: string, category: ?string, quantity: int, amount: float}
     * @param array|null $extra Extra parameter to send in the body check the API documentation
     *
     * @return TransactionResponse
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function makeCollect(
        $amount,
        $service,
        $payer,
        DateTime $date,
        $nonce,
        $trxID = null,
        $country = null,
        $currency = null,
        $feesIncluded = null,
        $mode = null,
        $conversion = null,
        array $location = null,
        array $customer = null,
        array $products = null,
        array $extra = null
    ) {
        if (is_null($country)) {
            $country = 'CM';
        }
        if (is_null($currency)) {
            $currency = 'XAF';
        }
        if (is_null($feesIncluded)) {
            $feesIncluded = true;
        }
        if (is_null($mode)) {
            $mode = 'synchronous';
        }
        if (is_null($conversion)) {
            $conversion = false;
        }

        $endpoint = 'payment/collect/';
        $url = $this->buildUrl($endpoint);

        $body = [
            'amount' => $amount,
            'service' => $service,
            'payer' => $payer,
            'country' => $country,
            'currency' => $currency,
            'fees' => $feesIncluded,
            'conversion' => $conversion,
        ];
        if (!is_null($location)) {
            $body['location'] = $location;
        }
        if (!is_null($customer)) {
            $body['customer'] = $customer;
        }
        if (!is_null($products)) {
            $body['products'] = $products;
        }
        if ($extra != null) {
            $body = array_merge($body, $extra);
        }

        $authorization = $this->getAuthorization('POST', $endpoint, $date, $nonce, ['content-type' => 'application/json'], $body);

        $headers = [
            "x-mesomb-date: ".$date->getTimestamp(),
            'x-mesomb-nonce: '.$nonce,
            'Authorization: '.$authorization,
            'Content-Type: application/json',
            'X-MeSomb-Application: '.$this->applicationKey,
            'X-MeSomb-OperationMode: '.$mode,
        ];
        if (!is_null($trxID)) {
            $headers[] = 'X-MeSomb-TrxID: '.$trxID;
        }

        $client = new CurlClient();
        list($rbody, $rcode, $rheaders) = $client->request('post', $url, $headers, $body);
        if ($rcode >= 300) {
            $this->processClientException($rcode, $rbody);
        }
        return new TransactionResponse(json_decode($rbody, true));
    }

    /**
     * Make a deposit in a receiver mobile account.
     *
     * @param int $amount the amount of the transaction
     * @param string $service service code (MTN, ORANGE, AIRTEL, ...)
     * @param string $receiver receiver account (in the local phone number)
     * @param DateTime $date date of the request
     * @param string $nonce Unique key generated for each transaction
     * @param string|null $trxID ID of the transaction in your local system
     * @param string|null $country country code 'CM' by default
     * @param string|null $currency currency of the transaction (XAF, XOF, ...) XAF by default
     * @param array|null $extra Extra parameter to send in the body check the API documentation
     * @return TransactionResponse
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function makeDeposit($amount, $service, $receiver, DateTime $date, $nonce, $trxID = null, $country = null, $currency = null, array $extra = null) {
        if (is_null($country)) {
            $country = 'CM';
        }
        if (is_null($currency)) {
            $currency = 'XAF';
        }

        $endpoint = 'payment/deposit/';
        $url = $this->buildUrl($endpoint);

        $body = [
            'amount' => $amount,
            'receiver' => $receiver,
            'service' => $service,
            'country' => $country,
            'currency' => $currency,
        ];

        if ($extra != null) {
            $body = array_merge($body, $extra);
        }

        $authorization = $this->getAuthorization('POST', $endpoint, $date, $nonce, ['content-type' => 'application/json'], $body);

        $headers = [
            'x-mesomb-date: '.$date->getTimestamp(),
            'x-mesomb-nonce: '.$nonce,
            'Authorization: '.$authorization,
            'Content-Type: '.'application/json',
            'X-MeSomb-Application: '.$this->applicationKey,
        ];
        if (!is_null($trxID)) {
            $headers[] = 'X-MeSomb-TrxID: '.$trxID;
        }

        $client = new CurlClient();
        list($rbody, $rcode, $rheaders) = $client->request('post', $url, $headers, $body);
        if ($rcode >= 300) {
            $this->processClientException($rcode, $rbody);
        }
        return new TransactionResponse(json_decode($rbody, true));
    }

    /**
     * Update security settings of your service on MeSomb
     *
     * @param string $field which security field you want to update (check API documentation)
     * @param string $action SET or UNSET
     * @param mixed|null $value value of the field
     * @param DateTime|null $date date of the request
     * @return Application
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function updateSecurity($field, $action, $value = null, DateTime $date = null)
    {
        $endpoint = 'payment/security/';
        $url = $this->buildUrl($endpoint);

        if ($date == null) {
            $date = new DateTime();
        }

        $body = [
            'field' => $field,
            'action' => $action,
        ];
        if ($action !== 'UNSET') {
            $body['value'] = $value;
        }

        $authorization = $this->getAuthorization('POST', $endpoint, $date, '', ['content-type' => 'application/json'], $body);

        $client = new CurlClient();
        list($rbody, $rcode, $rheaders) = $client->request('post', $url, [
            'x-mesomb-date: '.$date->getTimestamp(),
            'x-mesomb-nonce: ',
            'Authorization: '.$authorization,
            'Content-Type: application/json',
            'X-MeSomb-Application:'.$this->applicationKey,
        ], $body);
        if ($rcode >= 300) {
            $this->processClientException($rcode, $rbody);
        }

        return new Application(json_decode($rbody, true));
    }

    /**
     * Get the current status of your service on MeSomb
     *
     * @param DateTime|null $date date of the request
     * @return Application
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function getStatus(DateTime $date = null)
    {
        $endpoint = 'payment/status/';

        if ($date == null) {
            $date = new DateTime();
        }

        $authorization = $this->getAuthorization('GET', $endpoint, $date, '');

        $client = new CurlClient();
        list($rbody, $rcode, $rheaders) = $client->request('get', $this->buildUrl($endpoint), [
            'x-mesomb-date: '.$date->getTimestamp(),
            'x-mesomb-nonce: ',
            'Authorization: '.$authorization,
            'X-MeSomb-Application:'.$this->applicationKey,
        ], null);

        if ($rcode >= 300) {
            $this->processClientException($rcode, $rbody);
        }

        return new Application(json_decode($rbody, true));
    }

    /**
     * Get transactions from MeSomb by IDs.
     *
     * @param array $ids list of ids
     * @param DateTime|null $date date of the request
     * @return mixed|void
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function getTransactions(array $ids, DateTime $date = null)
    {
        $endpoint = "payment/transactions/?ids=".implode(',', $ids);

        if ($date == null) {
            $date = new DateTime();
        }

        $authorization = $this->getAuthorization('GET', $endpoint, $date, '');

        $client = new CurlClient();
        list($rbody, $rcode, $rheaders) = $client->request('get', $this->buildUrl($endpoint), [
            'x-mesomb-date: '.$date->getTimestamp(),
            'x-mesomb-nonce: ',
            'Authorization: '.$authorization,
            'X-MeSomb-Application:'.$this->applicationKey,
        ], null);

        if ($rcode >= 300) {
            $this->processClientException($rcode, $rbody);
        }

        return json_decode($rbody, true);
    }
}
