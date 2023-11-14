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
use MeSomb\Model\Transaction;
use MeSomb\Model\TransactionResponse;
use MeSomb\Signature;
use MeSomb\Util\Util;

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

    private function executeRequest($method, $endpoint, $date, $nonce, $body = null, $mode = null) {
        $headers = [
            "x-mesomb-date: ".$date->getTimestamp(),
            'x-mesomb-nonce: '.$nonce,
            'Content-Type: application/json',
            'X-MeSomb-Application: '.$this->applicationKey,
            'X-MeSomb-OperationMode: '.$mode,
        ];
        if (!is_null(Util::getOrDefault($body, 'trxID'))) {
            $headers[] = 'X-MeSomb-TrxID: '.$body['trxID'];
            unset($body['trxID']);
        }
        if ($body) {
            $body['source'] = 'MeSombPHP/v'.MeSomb::$version;
        }
        if ($method == 'POST') {
            $authorization = $this->getAuthorization($method, $endpoint, $date, $nonce, ['content-type' => 'application/json'], $body);
        } else {
            $authorization = $this->getAuthorization($method, $endpoint, $date, $nonce);
        }
        $headers[] = 'Authorization: '.$authorization;

        $client = new CurlClient();
        $url = $this->buildUrl($endpoint);
        list($rbody, $rcode, $rheaders) = $client->request($method, $url, $headers, $body);
        if ($rcode >= 300) {
            $this->processClientException($rcode, $rbody);
        }
        return json_decode($rbody, true);
    }

    /**
     * Collect money from a mobile account
     *
     * @param array{foo: string, bar: int} $params
     * @param string $params[amount] - Amount of the transaction
     * @param array-key $params with the below information
     *               - amount: amount to collect
     *               - service: payment service with the possible values MTN, ORANGE, AIRTEL
     *               - payer: account number to collect from
     *               - date: date of the request
     *               - nonce: unique string on each request
     *               - country: 2 letters country code of the service (configured during your service registration in MeSomb)
     *               - currency: currency of your service depending on your country
     *               - fees: false if your want MeSomb fees to be computed and included in the amount to collect
     *               - mode: asynchronous or synchronous
     *               - conversion: true in case of foreign currently defined if you want to rely on MeSomb to convert the amount in the local currency
     *               - location: Map containing the location of the customer with the following attributes: town, region and location all string.
     *               - products: It is ArrayList of products. Each product are Map with the following attributes: name string, category string, quantity int and amount float
     *               - customer: a Map containing information about the customer: phone string, email: string, first_name string, last_name string, address string, town string, region string and country string
     *               - trxID: if you want to include your transaction ID in the request
     *               - extra: Map to add some extra attribute depending on the API documentation
     *
     * @return TransactionResponse
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function makeCollect(array $params) {
        $endpoint = 'payment/collect/';

        $date = Util::getOrDefault($params, 'date', new DateTime());
        $nonce = $params['nonce'];

        $body = [
            'amount' => $params['amount'],
            'service' => $params['service'],
            'payer' => $params['payer'],
            'country' => Util::getOrDefault($params, 'country', 'CM'),
            'currency' => Util::getOrDefault($params, 'currency', 'XAF'),
            'fees' => Util::getOrDefault($params, 'fees', true),
            'conversion' => Util::getOrDefault($params, 'conversion', false),
        ];
        if (!is_null(Util::getOrDefault($params, 'trxID'))) {
            $body['trxID'] = $params['trxID'];
        }
        if (!is_null(Util::getOrDefault($params, 'location'))) {
            $body['location'] = $params['location'];
        }
        if (!is_null(Util::getOrDefault($params, 'customer'))) {
            $body['customer'] = $params['customer'];
        }
        if (!is_null(Util::getOrDefault($params, 'products'))) {
            $body['products'] = $params['products'];
        }
        if (!is_null(Util::getOrDefault($params, 'extra'))) {
            $body = array_merge($body, $params['extra']);
        }

        return new TransactionResponse($this->executeRequest('POST', $endpoint, $date, $nonce, $body, Util::getOrDefault($params, 'mode', 'synchronous')));
    }

    /**
     * Make a deposit in a receiver mobile account.
     *
     * @param array-key $params with the below information
     *               - amount: amount to collect
     *               - service: payment service with the possible values MTN, ORANGE, AIRTEL
     *               - receiver: account number to depose money
     *               - date: date of the request
     *               - nonce: unique string on each request
     *               - country: 2 letters country code of the service (configured during your service registration in MeSomb)
     *               - currency: currency of your service depending on your country
     *               - conversion: true in case of foreign currently defined if you want to rely on MeSomb to convert the amount in the local currency
     *               - location: Map containing the location of the customer with the following attributes: town, region and location all string.
     *               - products: It is array of products. Each product are Map with the following attributes: name string, category string, quantity int and amount float
     *               - customer: a Map containing information about the customer: phone string, email: string, first_name string, last_name string, address string, town string, region string and country string
     *               - trxID: if you want to include your transaction ID in the request
     *               - extra: Map to add some extra attribute depending on the API documentation
     * @return TransactionResponse
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function makeDeposit(array $params) {
        $endpoint = 'payment/deposit/';
        $url = $this->buildUrl($endpoint);

        $date = Util::getOrDefault($params, 'date', new DateTime());
        $nonce = $params['nonce'];

        $body = [
            'amount' => $params['amount'],
            'service' => $params['service'],
            'receiver' => $params['receiver'],
            'country' => Util::getOrDefault($params, 'country', 'CM'),
            'currency' => Util::getOrDefault($params, 'currency', 'XAF'),
        ];
        if (!is_null(Util::getOrDefault($params, 'trxID'))) {
            $body['trxID'] = $params['trxID'];
        }
        if (!is_null(Util::getOrDefault($params, 'location'))) {
            $body['location'] = $params['location'];
        }
        if (!is_null(Util::getOrDefault($params, 'customer'))) {
            $body['customer'] = $params['customer'];
        }
        if (!is_null(Util::getOrDefault($params, 'products'))) {
            $body['products'] = $params['products'];
        }
        if (!is_null(Util::getOrDefault($params, 'extra'))) {
            $body = array_merge($body, $params['extra']);
        }

        return new TransactionResponse($this->executeRequest('POST', $endpoint, $date, $nonce, $body, Util::getOrDefault($params, 'mode', 'synchronous')));
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

        return new Application($this->executeRequest('POST', $endpoint, $date, '', $body));
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

        return new Application($this->executeRequest('GET', $endpoint, $date, ''));
    }

    /**
     * Get transactions stored in MeSomb based on the list
     *
     * @param array $ids list of ids
     * @param DateTime|null $date date of the request
     * @return mixed|void
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function getTransactions(array $ids, $source = 'MESOMB')
    {
        $endpoint = "payment/transactions/?ids=".implode(',', $ids)."&source=".$source;

        return array_map(function ($v) {
            return new Transaction($v);
        }, $this->executeRequest('GET', $endpoint, new DateTime(), ''));
    }

    /**
     * Reprocess transaction at the operators level to confirm the status of a transaction
     *
     * @param array $ids list of ids
     * @param DateTime|null $date date of the request
     * @return mixed|void
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function checkTransactions(array $ids, $source = 'MESOMB')
    {
        $endpoint = "payment/transactions/check/?ids=".implode(',', $ids)."&source=".$source;

        return array_map(function ($v) {
            return new Transaction($v);
        }, $this->executeRequest('GET', $endpoint, new DateTime(), ''));
    }
}
