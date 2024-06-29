<?php

namespace MeSomb\Model;

use MeSomb\Util\Util;

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
        $this->name = Util::getOrDefault($data, 'name');
        $this->category = Util::getOrDefault($data, 'category');
        $this->quantity = Util::getOrDefault($data, 'quantity');
        $this->amount = Util::getOrDefault($data, 'amount');
    }
}
