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
use MeSomb\Util\RandomGenerator;
use MeSomb\Util\Util;

/**
 * Containing all operations provided by MeSomb Payment Service.
 *
 * [Check the documentation here](https://mesomb.hachther.com/en/api/v1.1/schema/)
 */
class PaymentOperation extends AOperation
{
    protected $service = 'payment';

    /**
     * PaymentOperation constructor.
     *
     * @param string $applicationKey
     * @param string $accessKey
     * @param string $secretKey
     */
    public function __construct($applicationKey, $accessKey, $secretKey)
    {
        parent::__construct($applicationKey, $accessKey, $secretKey);
    }

    /**
     * Collect money from a mobile account
     *
     * @param array{
     *     amount: float,
     *     service: string,
     *     payer: string,
     *     date?: DateTime,
     *     nonce?: string,
     *     country?: string,
     *     currency?: string,
     *     fees?: bool,
     *     mode?: string,
     *     conversion?: bool,
     *     location?: array{
     *         town: string,
     *         region: string,
     *         location: string
     *     },
     *     products?: array{
     *         name: string,
     *         category: string,
     *         quantity: int,
     *         amount: float
     *     },
     *     customer?: array{
     *         phone: string,
     *         email: string,
     *         first_name: string,
     *         last_name: string,
     *         address: string,
     *         town: string,
     *         region: string,
     *         country: string
     *     },
     *     trxID?: string,
     * } $params
     *
     * @return TransactionResponse
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function makeCollect(array $params) {
        $endpoint = 'payment/collect/';

        $date = Util::getOrDefault($params, 'date', new DateTime());

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

        return new TransactionResponse($this->executeRequest('POST', $endpoint, $date, Util::getOrDefault($params, 'nonce', RandomGenerator::nonce()), $body, Util::getOrDefault($params, 'mode', 'synchronous')));
    }

    /**
     * Collect money from a mobile account
     *
     * @param array{
     *     amount: float,
     *     service: string,
     *     receiver: string,
     *     date?: DateTime,
     *     nonce?: string,
     *     country?: string,
     *     currency?: string,
     *     mode?: string,
     *     location?: array{
     *         town: string,
     *         region: string,
     *         location: string
     *     },
     *     products?: array{
     *         name: string,
     *         category: string,
     *         quantity: int,
     *         amount: float
     *     },
     *     customer?: array{
     *         phone: string,
     *         email: string,
     *         first_name: string,
     *         last_name: string,
     *         address: string,
     *         town: string,
     *         region: string,
     *         country: string
     *     },
     *     trxID?: string,
     * } $params
     *
     * @return TransactionResponse
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function makeDeposit(array $params) {
        $endpoint = 'payment/deposit/';

        $date = Util::getOrDefault($params, 'date', new DateTime());

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

        return new TransactionResponse($this->executeRequest('POST', $endpoint, $date, Util::getOrDefault($params, 'nonce', RandomGenerator::nonce()), $body, Util::getOrDefault($params, 'mode', 'synchronous')));
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

    /**
     * @param string $id
     * @param array $params
     * @return TransactionResponse
     */
    public function refundTransaction(string $id, array $params = []) {
        $endpoint = 'payment/refund/';

        $date = Util::getOrDefault($params, 'date', new DateTime());

        $body = [
            'id' => $id,
            'conversion' => Util::getOrDefault($params, 'conversion', false),
            'currency' => Util::getOrDefault($params, 'currency', 'XAF'),
        ];
        if (!is_null(Util::getOrDefault($params, 'amount'))) {
            $body['amount'] = $params['amount'];
        }

        return new TransactionResponse($this->executeRequest('POST', $endpoint, $date, Util::getOrDefault($params, 'nonce', RandomGenerator::nonce()), $body, Util::getOrDefault($params, 'mode', 'synchronous')));
    }
}
