<?php

namespace MeSomb\Operation;

use MeSomb\Exception\InvalidClientRequestException;
use MeSomb\Exception\PermissionDeniedException;
use MeSomb\Exception\ServerException;
use MeSomb\Exception\ServiceNotFoundException;
use MeSomb\HttpClient\CurlClient;
use MeSomb\MeSomb;
use MeSomb\Signature;
use MeSomb\Util\Util;

abstract class AOperation
{
    /**
     * Your service application key on MeSomb
     *
     * @var string $applicationKey
     */
    protected $target;

    /**
     * Your access key provided by MeSomb
     *
     * @var string $accessKey
     */
    protected $accessKey;

    /**
     * Your secret key provided by MeSomb
     *
     * @var string $secretKey
     */
    protected $secretKey;

    /**
     * @var string $service
     */
    protected $service;

    /**
     * @param string $target
     * @param string $accessKey
     * @param string $secretKey
     */
    protected function __construct($target, $accessKey, $secretKey)
    {
        $this->target = $target;
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    protected function buildUrl($endpoint) {
        $host = MeSomb::$apiBase;
        $apiVersion = MeSomb::$apiVersion;
        return "$host/api/$apiVersion/$endpoint";
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
    protected function getAuthorization($method, $endpoint, $date, $nonce, array $headers = [], array $body = null)
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
    protected function processClientException($statusCode, $response) {
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

    protected function executeRequest($method, $endpoint, $date, $nonce, $body = null, $mode = null) {
        $headers = [
            "x-mesomb-date: ".$date->getTimestamp(),
            'x-mesomb-nonce: '.$nonce,
            'Content-Type: application/json',
            'X-MeSomb-OperationMode: '.$mode,
        ];
        if ($this->service == 'payment') {
            $headers[] = 'X-MeSomb-Application: '.$this->target;
        }
        if ($this->service == 'fundraising') {
            $headers[] = 'X-MeSomb-Fund: '.$this->target;
        }
        if ($this->service == 'wallet') {
            $headers[] = 'X-MeSomb-Provider: '.$this->target;
        }
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
}