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
 * [Check the documentation here](https://mesomb.hachther.com/en/api/v1.1/schema/)
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
     * Collect money from a mobile account
     *
     * @param int $amount amount to collect
     * @param string $service MTN, ORANGE, AIRTEL
     * @param string $payer account number to collect from
     * @param DateTime $date date of the request
     * @param string $nonce unique string on each request
     * @param string|null $trxID unique string in your local system
     * @param string $country country CM, NE
     * @param string $currency code of the currency of the amount
     * @param bool $feesIncluded if you want MeSomb to deduct he fees in the collected amount
     * @param string $mode asynchronous or synchronous
     * @param bool $conversion In case of foreign currently defined if you want to rely on MeSomb to convert the amount in the local currency
     * @param array<string, string>|null $location array-key containing the location of the customer ({town: string, region: string, country: string}) check the documentation.
     * @param array<string, string>|null $customer array-key containing information of the customer ({email: string, phone: string, town: string, region: string, country: string, first_name: string, last_name: string, address: string}) check the documentation
     * @param array<array<string, string>>|null $products array of product contained in the transaction. Product in this format array{id: string, name: string, category: ?string, quantity: int, amount: float}
     * @param array|null $extra Extra parameter to send in the body check the API documentation
     *
     * @return TransactionResponse|void
     * @throws GuzzleException
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function makeCollect(
        int $amount,
        string $service,
        string $payer,
        DateTime $date,
        string $nonce,
        string $trxID = null,
        string $country = 'CM',
        string $currency = 'XAF',
        bool $feesIncluded = true,
        string $mode = 'synchronous',
        bool $conversion = false,
        array $location = null,
        array $customer = null,
        array $products = null,
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
            'x-mesomb-date' => $date->getTimestamp(),
            'x-mesomb-nonce' => $nonce,
            'Authorization' => $authorization,
            'Content-Type'     => 'application/json',
            'X-MeSomb-Application' => $this->applicationKey,
            'X-MeSomb-OperationMode' => $mode,
        ];
        if (!is_null($trxID)) {
            $headers['X-MeSomb-TrxID'] = $trxID;
        }

        $client = new Client();
        try {
            $response = $client->post($url, [
                'body' => json_encode($body, JSON_UNESCAPED_SLASHES),
                'headers' => $headers
            ]);
            return new TransactionResponse(json_decode($response->getBody()->getContents(), true));
        } catch (ClientException $exception) {
            $this->processClientException($exception);
        } catch (Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception->getCode());
        }
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
     * @param string $country country code 'CM' by default
     * @param string $currency currency of the transaction (XAF, XOF, ...) XAF by default
     * @param array|null $extra Extra parameter to send in the body check the API documentation
     * @return TransactionResponse|void
     * @throws GuzzleException
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function makeDeposit(int $amount, string $service, string $receiver, DateTime $date, string $nonce, string $trxID = null, string $country = 'CM', string $currency = 'XAF', array $extra = null) {
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
            'x-mesomb-date' => $date->getTimestamp(),
            'x-mesomb-nonce' => $nonce,
            'Authorization' => $authorization,
            'Content-Type'     => 'application/json',
            'X-MeSomb-Application' => $this->applicationKey,
        ];
        if (!is_null($trxID)) {
            $headers['X-MeSomb-TrxID'] = $trxID;
        }

        $client = new Client();
        try {
            $response = $client->post($url, [
                'body' => json_encode($body, JSON_UNESCAPED_SLASHES),
                'headers' => $headers
            ]);
            return new TransactionResponse(json_decode($response->getBody()->getContents(), true));
        } catch (ClientException $exception) {
            $this->processClientException($exception);
        } catch (Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Update security settings of your service on MeSomb
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
