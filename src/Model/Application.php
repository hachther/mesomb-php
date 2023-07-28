<?php

namespace MeSomb\Model;

class Application
{
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
     * @var bool if your app can receive live transaction
     */
    public $isLive;

    /**
     * @var string the name of your service
     */
    public $name;

    /**
     * @var array security setting of your service at MeSomb
     */
    public $security;

    /**
     * @var string current status of your application
     */
    public $status;

    /**
     * @var string|null the url of your service
     */
    public $url;

    public function __construct($data) {
        $this->key = $data['key'];
        $this->logo = $data['logo'];
        $this->balances = array_map(function ($v) {
            return new ApplicationBalance($v);
        }, $data['balances']);
        $this->countries = $data['countries'];
        $this->description = $data['description'];
        $this->isLive = $data['is_live'];
        $this->name = $data['name'];
        $this->security = $data['security'];
        $this->status = $data['status'];
        $this->url = $data['url'];
    }

    /**
     * Get a specific setting value
     *
     * @param string $field
     * @return mixed
     */
    public function getSecurityField(string $field) {
        return isset($this->security[$field]) ? $this->security[$field] : null;
    }

    /**
     * Get current balance
     *
     * @param string|null $country
     * @param string|null $service
     * @return int
     */
    public function getBalance(string $country = null, string $service = null)
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
}