<?php

namespace MeSomb\Model;

class Application
{
    /**
     * @var string Application key
     */
    private $key;

    /**
     * @var string|null url of your logo
     */
    private $logo;

    /**
     * @var array your current balance in your different account
     */
    private $balances;

    /**
     * @var array list of countries where your service is available
     */
    private $countries;

    /**
     * @var string|null description of your service at MeSomb
     */
    private $description;

    /**
     * @var bool if your app can receive live transaction
     */
    private $isLive;

    /**
     * @var string the name of your service
     */
    private $name;

    /**
     * @var array security setting of your service at MeSomb
     */
    private $security;

    /**
     * @var string current status of your application
     */
    private $status;

    /**
     * @var string|null the url of your service
     */
    private $url;

    public function __construct($data) {
        $this->key = $data['key'];
        $this->logo = $data['logo'];
        $this->balances = $data['balances'];
        $this->countries = $data['countries'];
        $this->description = $data['description'];
        $this->isLive = $data['is_live'];
        $this->name = $data['name'];
        $this->security = $data['security'];
        $this->status = $data['status'];
        $this->url = $data['url'];
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @return array
     */
    public function getBalances()
    {
        return $this->balances;
    }

    /**
     * @return array
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isLive()
    {
        return $this->isLive;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getSecurity()
    {
        return $this->security;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
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
                return $val['country'] == $country;
            });
        }
        if ($service != null) {
            $data = array_filter($data, function ($val) use ($service) {
                return $val['provider'] == $service;
            });
        }

        return array_reduce($data, function ($acc, $item) {
            return $acc + $item['value'];
        }, 0);
    }
}