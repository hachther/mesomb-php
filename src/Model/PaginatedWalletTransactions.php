<?php

namespace MeSomb\Model;

/**
 * Class PaginatedWalletTransactions
 * @package MeSomb\Model
 * @extends APaginated
 *
 * @property WalletTransaction[] results - List of wallet transactions
 */
class PaginatedWalletTransactions extends APaginated
{
    /**
     * @var WalletTransaction[]
     */
    public $results;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->results = array_map(function ($transaction) {
            return new WalletTransaction($transaction);
        }, $data['results']);
    }
}