<?php

namespace MeSomb\Model;

use DateTime;

/**
 * Class WalletTransaction
 * @package MeSomb\Model
 *
 * @property string $id - The transaction's id
 * @property string $status - The transaction's status
 * @property string $type - The transaction's type
 * @property numeric $amount - The transaction's amount
 * @property numeric $direction - The transaction's direction
 * @property string $wallet - The transaction's wallet
 * @property numeric $balanceAfter - The balance after the transaction
 * @property DateTime $date - The transaction's date
 * @property string $country - The transaction's country
 * @property string $finTrxId - The transaction's financial transaction id
 */
class WalletTransaction
{
    /**
     * @var number
     */
    public $id;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $type;

    /**
     * @var double
     */
    public $amount;

    /**
     * @var int
     */
    public $direction;

    /**
     * @var int
     */
    public $wallet;

    /**
     * @var double
     */
    public $balance_after;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $fin_trx_id;
    /**
     * @var mixed
     */
    private $data;

    public function __construct($data)
    {
        $this->data = $data;

        $this->id = $data['id'];
        $this->status = $data['status'];
        $this->type = $data['type'];
        $this->amount = $data['amount'];
        $this->direction = $data['direction'];
        $this->wallet = $data['wallet'];
        $this->balance_after = $data['balance_after'];
        $this->date = new DateTime($data['date']);
        $this->country = $data['country'];
        $this->fin_trx_id = $data['fin_trx_id'];
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}