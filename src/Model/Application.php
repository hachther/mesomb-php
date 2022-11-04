<?php

namespace MeSomb\Model;

class Application
{
    /**
     * @var string Application key
     */
    private string $key;

    /**
     * @var string|null url of your logo
     */
    private ?string $logo;

    /**
     * @var array your current balance in your different account
     */
    private array $balances;

    /**
     * @var array list of countries where your service is available
     */
    private array $countries;

    /**
     * @var string|null description of your service at MeSomb
     */
    private ?string $description;

    /**
     * @var bool if your app can receive live transaction
     */
    private bool $isLive;

    /**
     * @var string the name of your service
     */
    private string $name;

    /**
     * @var array security setting of your service at MeSomb
     */
    private array $security;

    /**
     * @var string current status of your application
     */
    private string $status;

    /**
     * @var string|null the url of your service
     */
    private ?string $url;

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
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getLogo(): string
    {
        return $this->logo;
    }

    /**
     * @return array
     */
    public function getBalances(): array
    {
        return $this->balances;
    }

    /**
     * @return array
     */
    public function getCountries(): array
    {
        return $this->countries;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isLive(): bool
    {
        return $this->isLive;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getSecurity(): array
    {
        return $this->security;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get a specific setting value
     *
     * @param string $field
     * @return mixed
     */
    public function getSecurityField(string $field): mixed {
        return $this->security[$field] ?? null;
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