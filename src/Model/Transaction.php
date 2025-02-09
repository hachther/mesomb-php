<?php

namespace MeSomb\Model;

class Transaction extends ATransaction
{
    /**
     * @var Customer
     */
    public $customer = null;

    /**
     * @var Product[]
     */
    public $products = null;

    public function __construct($data)
    {
        parent::__construct($data);

        if (isset($data['customer'])) {
            $this->customer = new Customer($data['customer']);
        }
        if (isset($data['products'])) {
            $this->products = array_map(function ($v) {
                return new Product($v);
            }, $data['products']);
        }
    }
}