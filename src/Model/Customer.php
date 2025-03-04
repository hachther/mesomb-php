<?php

namespace MeSomb\Model;

use MeSomb\Util\Util;

/**
 * Class Customer
 * @package MeSomb\Model
 * 
 * @property string|null $email - The customer's email
 * @property string|null $phone - The customer's phone number
 * @property string|null $town - The customer's town
 * @property string|null $region - The customer's region
 * @property string|null $country - The customer's country
 * @property string|null $first_name - The customer's first name
 * @property string $last_name - The customer's last name
 * @property string|null $address - The customer's address
 */
class Customer
{
    /**
     * @var string|null
     */
    public $email;

    /**
     * @var string|null
     */
    public $phone;

    /**
     * @var string|null
     */
    public $town;

    /**
     * @var string|null
     */
    public $region;

    /**
     * @var string|null
     */
    public $country;

    /**
     * @var string|null
     */
    public $first_name;

    /**
     * @var string
     */
    public $last_name;

    /**
     * @var string|null
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
