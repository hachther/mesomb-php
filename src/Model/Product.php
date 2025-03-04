<?php

namespace MeSomb\Model;

use MeSomb\Util\Util;

/**
 * Class Product
 * @package MeSomb\Model
 *
 * @property string $id - The product's id
 * @property string $name - The product's name
 * @property string $category - The product's category
 * @property numeric $quantity - The product's quantity
 * @property numeric $amount - The product's amount
 */
class Product
{
    /**
     * @var string
     */
    public $id;

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
        $this->id = $data['id'];
        $this->name = Util::getOrDefault($data, 'name');
        $this->category = Util::getOrDefault($data, 'category');
        $this->quantity = Util::getOrDefault($data, 'quantity');
        $this->amount = Util::getOrDefault($data, 'amount');
    }
}
