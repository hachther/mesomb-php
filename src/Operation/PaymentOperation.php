<?php

namespace MeSomb\Operation;

use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use MeSomb\Exception\InvalidClientRequestException;
use MeSomb\Exception\PermissionDeniedException;
use MeSomb\Exception\ServerException;
use MeSomb\Exception\ServiceNotFoundException;
use MeSomb\Model\Application;
use MeSomb\Model\TransactionResponse;
use MeSomb\Settings;
use MeSomb\Signature;

/**
 * Containing all operations provided by MeSomb Payment Service.
 *
 * [Check the documentation here](https://mesomb.hachther.com/en/api/schema/)
 */
class PaymentOperation
{
    /**
     * Your service application key on MeSomb
     *
     * @var string $applicationKey
     */
    private string $applicationKey;

    /**
     * Your access key provided by MeSomb
     *
     * @var string $accessKey
     */
    private string $accessKey;

    /**
     * Your secret key provided by MeSomb
     *
     * @var string $secretKey
     */
    private string $secretKey;

    public function __construct(string $applicationKey, string $accessKey, string $secretKey)
    {
        $this->applicationKey = $applicationKey;
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    private function buildUrl($endpoint) {
        $host = Settings::$HOST;
        $apiVersion = Settings::$APIVERSION;
        return "$host/en/api/$apiVersion/$endpoint";
    }

    private function getAuthorization($method, $endpoint, $date, $nonce, array $headers = [], array $body = null): string
    {
        $url = $this->buildUrl($endpoint);

        $credentials = ['accessKey' => $this->accessKey, 'secretKey' => $this->secretKey];

        return Signature::signRequest('payment', $method, $url, $date, $nonce, $credentials, $headers, $body);
    }

    /**
     * @throws ServerException
     * @throws InvalidClientRequestException
     * @throws ServiceNotFoundException
     * @throws PermissionDeniedException
     */
    private function processClientException(ClientException $exception) {
        $code = null;
        $message = $exception->getResponse()->getBody()->getContents();
        if (str_starts_with($message, "{")) {
            $data = json_decode($message, true);
            $message = $data['detail'];
            $code = $data['code'];
        }
        throw match ($exception->getCode()) {
            404 => new ServiceNotFoundException($message),
            403, 401 => new PermissionDeniedException($message),
            400 => new InvalidClientRequestException($message, $code),
            default => new ServerException($message, $code),
        };
    }

    /**
     * Collect money a use account
     * [Check the documentation here](https://mesomb.hachther.com/en/api/schema/)
     *
     * @param int $amount amount to collect
     * @param string $service MTN, ORANGE, AIRTEL
     * @param string $payer account number to collect from
     * @param DateTime $date date of the request
     * @param string $nonce unique string on each request
     * @param string $country country CM, NE
     * @param string $currency code of the currency of the amount
     * @param bool $feesIncluded if your want MeSomb to include and compute fees in the amount to collect
     * @param string $mode asynchronous or synchronous
     * @param bool $conversion In case of foreign currently defined if you want to rely on MeSomb to convert the amount in the local currency
     * @param array|null $location dict containing the location of the customer check the documentation
     * @param array|null $customer dict containing information of the customer check the documentation
     * @param array|null $product dict containing information of the product check the documentation
     * @param array|null $extra Extra parameter to send in the body check the API documentation
     *
     * @return TransactionResponse|void
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     * @throws GuzzleException
     */
    public function makeCollect(
        int $amount,
        string $service,
        string $payer,
        DateTime $date,
        string $nonce,
        string $country = 'CM',
        string $currency = 'XAF',
        bool $feesIncluded = true,
        string $mode = 'synchronous',
        bool $conversion = false,
        array $location = null,
        array $customer = null,
        array $product = null,
        array $extra = null
    ) {
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
        if ($location != null) {
            $body['location'] = $location;
        }
        if ($customer != null) {
            $body['customer'] = $currency;
        }
        if ($product != null) {
            $body['product'] = $product;
        }
        if ($extra != null) {
            $body = array_merge($body, $extra);
        }

        $authorization = $this->getAuthorization('POST', $endpoint, $date, $nonce, ['content-type' => 'application/json'], $body);

        $client = new Client();
        try {
            $response = $client->post($url, [
                'body' => json_encode($body, JSON_UNESCAPED_SLASHES),
                'headers' => [
                    'x-mesomb-date' => $date->getTimestamp(),
                    'x-mesomb-nonce' => $nonce,
                    'Authorization' => $authorization,
                    'Content-Type'     => 'application/json',
                    'X-MeSomb-Application' => $this->applicationKey,
                    'X-MeSomb-OperationMode' => $mode,
                ]
            ]);
            return new TransactionResponse(json_decode($response->getBody()->getContents(), true));
        } catch (ClientException $exception) {
            $this->processClientException($exception);
        } catch (Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Method to make a deposit in a receiver mobile account.
     * [Check the documentation here](https://mesomb.hachther.com/en/api/schema/)
     *
     * @param int $amount the amount of the transaction
     * @param string $service service code (MTN, ORANGE, AIRTEL, ...)
     * @param string $receiver receiver account (in the local phone number)
     * @param DateTime $date date of the request
     * @param string $nonce Unique key generated for each transaction
     * @param string $country country code 'CM' by default
     * @param string $currency currency of the transaction (XAF, XOF, ...) XAF by default
     * @param array|null $extra Extra parameter to send in the body check the API documentation
     * @return TransactionResponse|void
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     * @throws GuzzleException
     */
    public function makeDeposit(int $amount, string $service, string $receiver, DateTime $date, string $nonce, string $country = 'CM', string $currency = 'XAF', array $extra = null) {
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

        $client = new Client();
        try {
            $response = $client->post($url, [
                'body' => json_encode($body, JSON_UNESCAPED_SLASHES),
                'headers' => [
                    'x-mesomb-date' => $date->getTimestamp(),
                    'x-mesomb-nonce' => $nonce,
                    'Authorization' => $authorization,
                    'Content-Type'     => 'application/json',
                    'X-MeSomb-Application' => $this->applicationKey,
                ]
            ]);
            return new TransactionResponse(json_decode($response->getBody()->getContents(), true));
        } catch (ClientException $exception) {
            $this->processClientException($exception);
        } catch (Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Update security parameters of your service on MeSomb
     *
     * @param string $field which security field you want to update (check API documentation)
     * @param string $action SET or UNSET
     * @param mixed|null $value value of the field
     * @param DateTime|null $date date of the request
     * @return Application|void
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     * @throws GuzzleException
     */
    public function updateSecurity(string $field, string $action, mixed $value = null, DateTime $date = null)
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

        $client = new Client();

        try {
            $response = $client->post($url, [
                'body' => json_encode($body, JSON_UNESCAPED_SLASHES),
                'headers' => [
                    'x-mesomb-date' => $date->getTimestamp(),
                    'x-mesomb-nonce' => '',
                    'Authorization' => $authorization,
                    'Content-Type'     => 'application/json',
                    'X-MeSomb-Application' => $this->applicationKey,
                ]
            ]);
            return new Application(json_decode($response->getBody()->getContents(), true));
        } catch (ClientException $exception) {
            $this->processClientException($exception);
        } catch (Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Get the current status of your service on MeSomb
     *
     * @param DateTime|null $date date of the request
     * @return Application|void
     * @throws GuzzleException
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
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

        $client = new Client();

        try {
            $response = $client->get($this->buildUrl($endpoint), [
                'headers' => [
                    'x-mesomb-date' => $date->getTimestamp(),
                    'x-mesomb-nonce' => '',
                    'Authorization' => $authorization,
                    'X-MeSomb-Application' => $this->applicationKey,
                ]
            ]);
            return new Application(json_decode($response->getBody()->getContents(), true));
        } catch (ClientException $exception) {
            $this->processClientException($exception);
        } catch (Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Get transactions from MeSomb by IDs.
     *
     * @param array $ids list of ids
     * @param DateTime|null $date date of the request
     * @return mixed|void
     * @throws GuzzleException
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

        $client = new Client();

        try {
            $response = $client->get($this->buildUrl($endpoint), [
                'headers' => [
                    'x-mesomb-date' => $date->getTimestamp(),
                    'x-mesomb-nonce' => '',
                    'Authorization' => $authorization,
                    'X-MeSomb-Application' => $this->applicationKey,
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $exception) {
            $this->processClientException($exception);
        } catch (Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception->getCode());
        }
    }
}
