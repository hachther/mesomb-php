<?php

namespace MeSomb\Model;

/**
 * Class PaginatedTransactions
 *
 * @package MeSomb\Model
 * @extends APaginated
 *
 * @property Wallet[] $results Results of the current page
 */
class PaginatedWallets extends APaginated
{
    /**
     * @var Wallet[]
     */
    public $results;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->results = array_map(function ($wallet) {
            return new Wallet($wallet);
        }, $data['results']);
    }
}