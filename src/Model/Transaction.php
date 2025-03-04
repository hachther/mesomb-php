<?php

namespace MeSomb\Model;

/**
 * Class Transaction
 * @package MeSomb\Model
 *
 * @property Customer[]|null customer - Customer details
 * @property Product[]|null products - List of products
 */
class Transaction extends ATransaction
{
    /**
     * @var Customer|null
     */
    public $customer = null;

    /**
     * @var Product[]|null
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