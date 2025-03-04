<?php

namespace MeSomb\Model;

use DateTime;
use MeSomb\Util\Util;

/**
 * Class ATransaction
 * @package MeSomb\Model
 * 
 * @property string $pk - Primary key of the transaction
 * @property string $status - Status of the transaction
 * @property string $type - Type of the transaction
 * @property numeric $amount - Amount of the transaction
 * @property numeric $fees - Fees of the transaction
 * @property string $b_party - B party of the transaction
 * @property string $message - Message of the transaction
 * @property string $service - Service of the transaction
 * @property string $reference - Reference of the transaction
 * @property DateTime $date - Date of the transaction
 * @property string $country - Country of the transaction
 * @property string $currency - Currency of the transaction
 * @property string $fin_trx_id - Financial transaction ID of the transaction
 * @property numeric $trxamount - Total transaction amount
 * @property Location $location - Location of the transaction
 */
abstract class ATransaction
{
    private $data;

    /**
     * @var string
     */
    public $pk;

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
     * @var double
     */
    public $fees;

    /**
     * @var string
     */
    public $b_party;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $service;

    /**
     * @var string
     */
    public $reference;

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
    public $currency;

    /**
     * @var string
     */
    public $fin_trx_id;

    /**
     * @var double
     */
    public $trxamount;

    /**
     * @var Location
     */
    public $location = null;

    public function __construct($data)
    {
        $this->data = $data;

        $this->pk = $data['pk'];
        $this->status = $data['status'];
        $this->type = $data['type'];
        $this->amount = $data['amount'];
        $this->fees = $data['fees'];
        $this->b_party = $data['b_party'];
        $this->message = Util::getOrDefault($data, 'message');
        $this->service = $data['service'];
        $this->reference = Util::getOrDefault($data, 'reference');
        $this->date = new DateTime($data['ts']);
        $this->country = $data['country'];
        $this->currency = $data['currency'];
        $this->fin_trx_id = Util::getOrDefault($data, 'fin_trx_id');
        $this->trxamount = Util::getOrDefault($data, 'trxamount');
        if (isset($data['location'])) {
            $this->location = new Location($data['location']);
        }
    }

    /**
     * Check if the transaction was successful.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->status === 'SUCCESS';
    }

    /**
     * Check if the transaction is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'PENDING';
    }

    /**
     * Check if the transaction is failed.
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === 'FAILED';
    }

    public function getData()
    {
        return $this->data;
    }
}