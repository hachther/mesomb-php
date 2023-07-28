<?php

namespace MeSomb\Model;

class Location
{
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

    public function __construct($data) {
        $this->town = $data['town'];
        $this->region = $data['region'];
        $this->country = $data['country'];
    }
}