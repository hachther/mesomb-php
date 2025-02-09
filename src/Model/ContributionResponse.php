<?php

namespace MeSomb\Model;

class ContributionResponse
{
    /** @var bool|mixed */
    public $success;

    /** @var string|mixed|null  */
    public $message;

    /** @var Contribution */
    public $contribution;

    /** @var string|mixed  */
    public $status;

    public function __construct($data)
    {
        $this->success = $data['success'];
        $this->message = $data['message'];
        $this->contribution = new Contribution($data['contribution']);
        $this->status = $data['status'];
    }

    public function isOperationSuccess()
    {
        return $this->success;
    }

    public function isContributionSuccess() {
        return $this->contribution && $this->contribution->isSuccess();
    }
}