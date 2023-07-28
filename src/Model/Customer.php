<?php

namespace MeSomb\Model;

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
        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->town = $data['town'];
        $this->region = $data['region'];
        $this->country = $data['country'];
        $this->first_name = $data['first_name'];
        $this->last_name = $data['last_name'];
        $this->address = $data['address'];
    }
}