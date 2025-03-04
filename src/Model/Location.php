<?php

namespace MeSomb\Model;

use MeSomb\Util\Util;

/**
 * Class Location
 * @package MeSomb\Model
 * 
 * @property string $town - The location's town
 * @property string|null $region - The location's region
 * @property string|null $country - The location's country
 */
class Location
{
    /**
     * @var string
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

    public function __construct($data) {
        $this->town = Util::getOrDefault($data, 'town');
        $this->region = Util::getOrDefault($data, 'region');
        $this->country = Util::getOrDefault($data, 'country');
    }
}
