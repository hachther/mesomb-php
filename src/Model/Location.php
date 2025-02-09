<?php

namespace MeSomb\Model;

use MeSomb\Util\Util;

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
        $this->town = Util::getOrDefault($data, 'town');
        $this->region = Util::getOrDefault($data, 'region');
        $this->country = Util::getOrDefault($data, 'country');
    }
}
