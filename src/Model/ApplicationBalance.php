<?php

namespace MeSomb\Model;

class ApplicationBalance
{
    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $provider;

    /**
     * @var double
     */
    public $value;

    /**
     * @var string
     */
    public $service_name;

    public function __construct($data) {
        $this->country = $data['country'];
        $this->currency = $data['currency'];
        $this->provider = $data['provider'];
        $this->value = $data['value'];
        $this->service_name = $data['service_name'];
    }
}