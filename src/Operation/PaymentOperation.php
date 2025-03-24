<?php

namespace MeSomb\Operation;

use DateTime;
use MeSomb\Exception\InvalidClientRequestException;
use MeSomb\Exception\PermissionDeniedException;
use MeSomb\Exception\ServerException;
use MeSomb\Exception\ServiceNotFoundException;
use MeSomb\Model\Application;
use MeSomb\Model\Transaction;
use MeSomb\Model\TransactionResponse;
use MeSomb\Util\RandomGenerator;
use MeSomb\Util\Util;

/**
 * Containing all operations provided by MeSomb Payment Service.
 *
 * Class PaymentOperation
 *
 * @package MeSomb\Operation
 *
 * @property string $applicationKey Your service application key on MeSomb
 * @property string $accessKey Your access key provided by MeSomb
 * @property string $secretKey Your secret key provided by MeSomb
 * @property string $language The language to be used for the response
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
     * @param string $language
     */
    public function __construct($applicationKey, $accessKey, $secretKey, $language = 'en')
    {
        parent::__construct($applicationKey, $accessKey, $secretKey, $language);
    }

    /**
     * Collect money from a mobile account
     *
     * @param array{
     *     amount: float,
     *     service: string,
     *     payer: string,
     *     nonce?: string,
     *     country?: string,
     *     currency?: string,
     *     fees?: bool,
     *     mode?: string,
     *     conversion?: bool,
     *     location?: array{
     *         town: string,
     *         region?: string,
     *         location?: string
     *     },
     *     products?: array{
     *         name: string,
     *         category?: string,
     *         quantity: int,
     *         amount: float
     *     },
     *     customer?: array{
     *         phone?: string,
     *         email?: string,
     *         first_name?: string,
     *         last_name: string,
     *         address?: string,
     *         town?: string,
     *         region?: string,
     *         country?: string
     *     },
     *     trxID?: string,
     * } $params
     *
     * @return TransactionResponse
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException
     * @throws PermissionDeniedException
     */
    public function makeCollect(array $params) {
        $endpoint = 'payment/collect/';

        assert($params['amount'] > 0);

        $body = [
            'amount' => $params['amount'],
            'service' => $params['service'],
            'payer' => $params['payer'],
            'country' => Util::getOrDefault($params, 'country', 'CM'),
            'currency' => Util::getOrDefault($params, 'currency', 'XAF'),
            'amount_currency' => Util::getOrDefault($params, 'currency', 'XAF'),
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

        return new TransactionResponse($this->executeRequest('POST', $endpoint, new DateTime(), Util::getOrDefault($params, 'nonce', RandomGenerator::nonce()), $body, Util::getOrDefault($params, 'mode', 'synchronous')));
    }

    /**
     * Collect money from a mobile account
     *
     * @param array{
     *     amount: float,
     *     service: string,
     *     receiver: string,
     *     nonce?: string,
     *     country?: string,
     *     currency?: string,
     *     mode?: string,
     *     location?: array{
     *         town: string,
     *         region?: string,
     *         location?: string
     *     },
     *     products?: array{
     *         name: string,
     *         category?: string,
     *         quantity: int,
     *         amount: float
     *     },
     *     customer?: array{
     *         phone?: string,
     *         email?: string,
     *         first_name?: string,
     *         last_name: string,
     *         address?: string,
     *         town?: string,
     *         region?: string,
     *         country?: string
     *     },
     *     trxID?: string,
     * } $params
     *
     * @return TransactionResponse
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException|PermissionDeniedException
     */
    public function makeDeposit(array $params) {
        $endpoint = 'payment/deposit/';

        assert($params['amount'] > 0);

        $body = [
            'amount' => $params['amount'],
            'service' => $params['service'],
            'receiver' => $params['receiver'],
            'country' => Util::getOrDefault($params, 'country', 'CM'),
            'currency' => Util::getOrDefault($params, 'currency', 'XAF'),
            'amount_currency' => Util::getOrDefault($params, 'currency', 'XAF'),
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

        return new TransactionResponse($this->executeRequest('POST', $endpoint, new DateTime(), Util::getOrDefault($params, 'nonce', RandomGenerator::nonce()), $body, Util::getOrDefault($params, 'mode', 'synchronous')));
    }

    /**
     * Send airtime to a customer
     *
     * @param array{
     *     amount: float,
     *     service: string,
     *     receiver: string,
     *     merchant: string,
     *     nonce?: string,
     *     country?: string,
     *     currency?: string,
     *     mode?: string,
     *     location?: array{
     *         town: string,
     *         region?: string,
     *         location?: string
     *     },
     *     products?: array{
     *         name: string,
     *         category?: string,
     *         quantity: int,
     *         amount: float
     *     },
     *     customer?: array{
     *         phone?: string,
     *         email?: string,
     *         first_name?: string,
     *         last_name: string,
     *         address?: string,
     *         town?: string,
     *         region?: string,
     *         country?: string
     *     },
     *     trxID?: string,
     * } $params
     *
     * @return TransactionResponse
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException|PermissionDeniedException
     */
    public function purchaseAirtime(array $params) {
        $endpoint = 'payment/airtime/';

        assert($params['amount'] > 0);

        $body = [
            'amount' => $params['amount'],
            'service' => $params['service'],
            'receiver' => $params['receiver'],
            'merchant' => $params['merchant'],
            'currency' => Util::getOrDefault($params, 'currency', 'XAF'),
            'amount_currency' => Util::getOrDefault($params, 'currency', 'XAF'),
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

        return new TransactionResponse($this->executeRequest('POST', $endpoint, new DateTime(), Util::getOrDefault($params, 'nonce', RandomGenerator::nonce()), $body, Util::getOrDefault($params, 'mode', 'synchronous')));
    }

    /**
     * Get the current status of your service on MeSomb
     *
     * @return Application
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException
     * @throws PermissionDeniedException
     */
    public function getStatus()
    {
        $endpoint = 'payment/status/';

        return new Application($this->executeRequest('GET', $endpoint, new DateTime(), ''));
    }

    /**
     * Get transactions stored in MeSomb based on the list
     *
     * @param array $ids list of ids
     * @return Transaction[]|void
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function getTransactions(array $ids, $source = 'MESOMB')
    {
        assert(count($ids) > 0);
        assert($source == 'MESOMB' || $source == 'EXTERNAL');

        $endpoint = "payment/transactions/?".implode('&', array_map(function ($id) {return 'ids='.$id;}, $ids))."&source=".$source;

        return array_map(function ($v) {
            return new Transaction($v);
        }, $this->executeRequest('GET', $endpoint, new DateTime(), ''));
    }

    /**
     * Reprocess transaction at the operators level to confirm the status of a transaction
     *
     * @param array $ids list of ids
     * @param string $source the source of the transaction
     *
     * @return Transaction[]|void
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function checkTransactions(array $ids, $source = 'MESOMB')
    {
        assert(count($ids) > 0);
        assert($source == 'MESOMB' || $source == 'EXTERNAL');

        $endpoint = "payment/transactions/?".implode('&', array_map(function ($id) {return 'ids='.$id;}, $ids))."&source=".$source;

        return array_map(function ($v) {
            return new Transaction($v);
        }, $this->executeRequest('GET', $endpoint, new DateTime(), ''));
    }

    /**
     * @param string $id The transaction's id
     * @param array{
     *     amount?: float,
     *     conversion?: bool,
     *     currency?: string,
     * } $params
     * @return TransactionResponse
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function refundTransaction($id, array $params = []) {
        $endpoint = 'payment/refund/';


        $body = [
            'id' => $id,
            'conversion' => Util::getOrDefault($params, 'conversion', false),
            'currency' => Util::getOrDefault($params, 'currency', 'XAF'),
            'amount_currency' => Util::getOrDefault($params, 'currency', 'XAF'),
        ];
        if (!is_null(Util::getOrDefault($params, 'amount'))) {
            $body['amount'] = $params['amount'];
        }

        return new TransactionResponse($this->executeRequest('POST', $endpoint, new DateTime(), Util::getOrDefault($params, 'nonce', RandomGenerator::nonce()), $body, Util::getOrDefault($params, 'mode', 'synchronous')));
    }
}
