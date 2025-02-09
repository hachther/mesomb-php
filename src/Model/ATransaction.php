<?php

namespace MeSomb\Model;

use DateTime;
use MeSomb\Util\Util;

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
     * @return bool
     */
    public function isSuccess()
    {
        return $this->status === 'SUCCESS';
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'PENDING';
    }

    /**
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