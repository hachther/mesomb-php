<?php

namespace MeSomb\Operation;

use DateTime;
use MeSomb\Exception\InvalidClientRequestException;
use MeSomb\Exception\PermissionDeniedException;
use MeSomb\Exception\ServerException;
use MeSomb\Exception\ServiceNotFoundException;
use MeSomb\Model\PaginatedWallets;
use MeSomb\Model\PaginatedWalletTransactions;
use MeSomb\Model\Wallet;
use MeSomb\Model\WalletTransaction;
use MeSomb\Util\RandomGenerator;
use MeSomb\Util\Util;

class WalletOperation extends AOperation
{
    protected $service = 'wallet';

    /**
     * FundraisingOperation constructor.
     *
     * @param string $providerKey
     * @param string $accessKey
     * @param string $secretKey
     * @param string $language
     */
    public function __construct($providerKey, $accessKey, $secretKey, $language = 'en')
    {
        parent::__construct($providerKey, $accessKey, $secretKey, $language);
    }

    /**
     * Create a new wallet
     *
     * @param array{
     *     first_name?: string,
     *     last_name: string,
     *     email?: string,
     *     phone_number: string,
     *     country?: string,
     *     gender: string,
     *     nonce?: string,
     *     number?: string,
     * } $params
     *
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function createWallet(array $params) {
        assert($params['gender'] == 'MAN' || $params['gender'] == 'WOMAN');

        $endpoint = 'wallet/wallets/';

        $params['country'] = Util::getOrDefault($params, 'country', 'CM');

        $nonce = Util::getOrDefault($params, 'nonce', RandomGenerator::nonce());
        unset($params['nonce']);

        return new Wallet($this->executeRequest('POST', $endpoint, new DateTime(), $nonce, $params, Util::getOrDefault($params, 'mode')));
    }

    /**
     * @param numeric $id
     *
     * @return Wallet
     *
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException|PermissionDeniedException
     */
    public function getWallet($id)
    {
        $endpoint = "wallet/wallets/$id/";

        return new Wallet($this->executeRequest('GET', $endpoint, new DateTime(), RandomGenerator::nonce(), []));
    }

    /**
     * @param numeric $page
     *
     * @return PaginatedWallets
     *
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException|PermissionDeniedException
     */
    public function getWallets($page = 1)
    {
        $endpoint = "wallet/wallets/?page=$page";

        return new PaginatedWallets($this->executeRequest('GET', $endpoint, new DateTime(), RandomGenerator::nonce(), []));
    }

    /**
     * Create a new wallet
     *
     * @param numeric $id
     * @param array{
     *     first_name?: string,
     *     last_name: string,
     *     email?: string,
     *     phone_number: string,
     *     country?: string,
     *     gender: string,
     *     nonce?: string,
     *     number?: string,
     * } $params
     *
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     * @throws ServiceNotFoundException
     */
    public function updateWallet($id, array $params) {
        assert(is_numeric($id));
        assert($params['gender'] == 'MAN' || $params['gender'] == 'WOMAN');

        $endpoint = "wallet/wallets/$id/";

        $params['country'] = Util::getOrDefault($params, 'country', 'CM');

        $nonce = Util::getOrDefault($params, 'nonce', RandomGenerator::nonce());
        unset($params['nonce']);

        return new Wallet($this->executeRequest('PUT', $endpoint, new DateTime(), $nonce, $params, Util::getOrDefault($params, 'mode')));
    }

    /**
     * @param numeric $id
     *
     * @return Wallet
     *
     * @throws InvalidClientRequestException
     * @throws ServerException
     * @throws ServiceNotFoundException|PermissionDeniedException
     */
    public function deleteWallet($id)
    {
        $endpoint = "wallet/wallets/$id/";

        return new Wallet($this->executeRequest('DELETE', $endpoint, new DateTime(), RandomGenerator::nonce(), []));
    }

    /**
     * Remove money to a wallet
     *
     * @param numeric $wallet id
     * @param numeric $amount
     * @param bool $force to force the operation event if the wallet has insufficient balance
     * @param string $message the message to attach to the transaction
     * @param string $externalId the external id of the transaction
     *
     * @return WalletTransaction
     *
     * @throws ServiceNotFoundException
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     */
    public function removeMoney($wallet, $amount, $force = false, $message = null, $externalId = null)
    {
        $endpoint = "wallet/wallets/$wallet/adjust/";

        $body = [
            'amount' => $amount,
            'force' => $force,
            'direction' => -1,
        ];

        if ($message != null) {
            $body['message'] = $message;
        }

        if ($externalId != null) {
            $body['trxID'] = $message;
        }

        return new WalletTransaction($this->executeRequest('POST', $endpoint, new DateTime(), RandomGenerator::nonce(), $body));
    }

    /**
     * Add money to a wallet
     *
     * @param numeric $wallet id
     * @param numeric $amount
     * @param string $message the message to attach to the transaction
     * @param string $externalId the external id of the transaction
     *
     * @return WalletTransaction
     *
     * @throws ServiceNotFoundException
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     */
    public function addMoney($wallet, $amount, $message = null, $externalId = null)
    {
        $endpoint = "wallet/wallets/$wallet/adjust/";

        $body = [
            'amount' => $amount,
            'direction' => 1,
        ];

        if ($message != null) {
            $body['message'] = $message;
        }

        if ($externalId != null) {
            $body['trxID'] = $message;
        }

        return new WalletTransaction($this->executeRequest('POST', $endpoint, new DateTime(), RandomGenerator::nonce(), $body));
    }

    /**
     * Transfer money from a wallet to another
     *
     * @param numeric $from ID the wallet to transfer money from
     * @param numeric $to ID the wallet to transfer money to
     * @param numeric $amount the amount to transfer
     * @param bool $force to force the operation event if the wallet has insufficient balance
     * @param string $message the message to attach to the transaction
     * @param string $externalId the external id of the transaction
     *
     * @return WalletTransaction
     *
     * @throws ServiceNotFoundException
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     */
    public function transferMoney($from, $to, $amount, $force = false, $message = null, $externalId = null)
    {
        $endpoint = "wallet/wallets/$from/transfer/";

        $body = [
            'amount' => $amount,
            'to' => $to,
            'force' => $force,
        ];

        if ($message != null) {
            $body['message'] = $message;
        }

        if ($externalId != null) {
            $body['trxID'] = $message;
        }

        return new WalletTransaction($this->executeRequest('POST', $endpoint, new DateTime(), RandomGenerator::nonce(), $body));
    }

    /**
     * Get the transactions of a wallet
     *
     * @param numeric $wallet ID the wallet to get transactions
     * @param numeric $page the page number
     *
     * @return PaginatedWalletTransactions
     *
     * @throws ServiceNotFoundException
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     */
    public function listTransactions($page = 1, $wallet = null)
    {
        $endpoint = "wallet/transactions/?page=$page".($wallet ? "&wallet=$wallet" : '');

        return new PaginatedWalletTransactions($this->executeRequest('GET', $endpoint, new DateTime(), RandomGenerator::nonce(), []));
    }

    /**
     * Get the transactions of a wallet
     *
     * @param array $ids ID the wallet to get transactions
     * @param string $source the source of the transaction
     *
     * @return WalletTransaction[]
     *
     * @throws ServiceNotFoundException
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     */
    public function getTransactions(array $ids, $source = 'MESOMB')
    {
        $endpoint = "wallet/transactions/search/?".implode('&', array_map(function ($id) {return 'ids='.$id;}, $ids))."&source=".$source;

        return array_map(function ($v) {
            return new WalletTransaction($v);
        }, $this->executeRequest('GET', $endpoint, new DateTime(), RandomGenerator::nonce()));
    }

    /**
     * Get the transactions of a wallet
     *
     * @param numeric $id ID the wallet to get transactions
     *
     * @return WalletTransaction
     *
     * @throws ServiceNotFoundException
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     */
    public function getTransaction($id) {
        $endpoint = "wallet/transactions/$id/";

        return new WalletTransaction($this->executeRequest('GET', $endpoint, new DateTime(), RandomGenerator::nonce(), []));
    }
}