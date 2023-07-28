<?php

namespace MeSomb\Model;

class Product
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $category;

    /**
     * @var double
     */
    public $quantity;

    /**
     * @var double
     */
    public $amount;

    public function __construct($data)
    {
        $this->name = $data['name'];
        $this->category = $data['category'];
        $this->quantity = isset($data['quantity']) ? $data['quantity'] : null;
        $this->amount = isset($data['amount']) ? $data['amount'] : null;
    }
}