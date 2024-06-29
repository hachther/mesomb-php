<?php

namespace MeSomb\Model;

use MeSomb\Util\Util;

class Customer
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $town;

    /**
     * @var string
     */
    public $region;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $first_name;

    /**
     * @var string
     */
    public $last_name;

    /**
     * @var string
     */
    public $address;

    public function __construct($data) {
        $this->email = Util::getOrDefault($data, 'email');
        $this->phone = Util::getOrDefault($data, 'phone');
        $this->town = Util::getOrDefault($data, 'town');
        $this->region = Util::getOrDefault($data, 'region');
        $this->country = Util::getOrDefault($data, 'country');
        $this->first_name = Util::getOrDefault($data, 'first_name');
        $this->last_name = Util::getOrDefault($data, 'last_name');
        $this->address = Util::getOrDefault($data, 'address');
    }
}
