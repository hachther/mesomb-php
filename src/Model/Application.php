<?php

namespace MeSomb\Model;

/**
 * Class Application
 * @package MeSomb\Model
 *
 * This class represents an application at MeSomb
 *
 * @property string $key Application key
 * @property string|null $logo url of your logo
 * @property array $balances your current balance in your different account
 * @property array $countries list of countries where your service is available
 * @property string|null $description description of your service at MeSomb
 * @property string $name the name of your service
 * @property array $security security setting of your service at MeSomb
 * @property string|null $url the url of your service
 */
class Application
{
    private $data;

    /**
     * @var string Application key
     */
    public $key;

    /**
     * @var string|null url of your logo
     */
    public $logo;

    /**
     * @var array your current balance in your different account
     */
    public $balances;

    /**
     * @var array list of countries where your service is available
     */
    public $countries;

    /**
     * @var string|null description of your service at MeSomb
     */
    public $description;

    /**
     * @var string the name of your service
     */
    public $name;

    /**
     * @var array security setting of your service at MeSomb
     */
    public $security;

    /**
     * @var string|null the url of your service
     */
    public $url;

    public function __construct($data) {
        $this->data = $data;

        $this->key = $data['key'];
        $this->logo = $data['logo'];
        $this->balances = array_map(function ($v) {
            return new ApplicationBalance($v);
        }, $data['balances']);
        $this->countries = $data['countries'];
        $this->description = $data['description'];
        $this->name = $data['name'];
        $this->security = $data['security'];
        $this->url = $data['url'];
    }

    /**
     * Get a specific setting value
     *
     * @param string $field
     * @return mixed
     */
    public function getSecurityField($field) {
        return isset($this->security[$field]) ? $this->security[$field] : null;
    }

    /**
     * Get current balance
     *
     * @param string|null $country
     * @param string|null $service
     * @return int
     */
    public function getBalance($country = null, $service = null)
    {
        $data = $this->balances;
        if ($country != null) {
            $data = array_filter($data, function ($val) use ($country) {
                return $val->country == $country;
            });
        }
        if ($service != null) {
            $data = array_filter($data, function ($val) use ($service) {
                return $val->provider == $service;
            });
        }

        return array_reduce($data, function ($acc, $item) {
            return $acc + $item->value;
        }, 0);
    }

    public function getData()
    {
        return $this->data;
    }
}