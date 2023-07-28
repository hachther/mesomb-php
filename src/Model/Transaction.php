<?php

namespace MeSomb\Model;

class Transaction
{
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
     * @var \DateTime
     */
    public $ts;

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
     * @var Customer | null
     */
    public $customer = null;

    /**
     * @var Location
     */
    public $location = null;

    /**
     * @var Product[]
     */
    public $products = null;

    public function __construct($data)
    {
        $this->pk = $data['pk'];
        $this->status = $data['status'];
        $this->type = $data['type'];
        $this->amount = $data['amount'];
        $this->fees = $data['fees'];
        $this->b_party = $data['b_party'];
        $this->message = isset($data['message']) ? $data['message'] : null;
        $this->service = $data['service'];
        $this->reference = isset($data['reference']) ? $data['reference'] : null;
        $this->ts = new \DateTime($data['ts']);
        $this->country = $data['country'];
        $this->currency = $data['currency'];
        $this->fin_trx_id = isset($data['fin_trx_id']) ? $data['fin_trx_id'] : null;
        $this->trxamount = isset($data['trxamount']) ? $data['trxamount'] : null;
        if (isset($data['customer'])) {
            $this->customer = new Customer($data['customer']);
        }
        if (isset($data['location'])) {
            $this->location = new Location($data['location']);
        }
        if (isset($data['products'])) {
            $this->products = array_map(function ($v) {
                return new Product($v);
            }, $data['products']);
        }
    }
}