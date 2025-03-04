<?php

namespace MeSomb\Model;

/**
 * Class ContributionResponse
 *
 * @property bool success - Indicates if the operation was successful
 * @property string|null message - The message of the response
 * @property Contribution contribution - The contribution
 * @property string status - The status of the transaction
 *
 * @package MeSomb\Model
 */
class ContributionResponse
{
    /** @var bool */
    public $success;

    /** @var string|null  */
    public $message;

    /** @var Contribution */
    public $contribution;

    /** @var string  */
    public $status;

    public function __construct($data)
    {
        $this->success = $data['success'];
        $this->message = $data['message'];
        $this->contribution = new Contribution($data['contribution']);
        $this->status = $data['status'];
    }

    /**
     * Check if the operation was successful.
     *
     * @return bool
     */
    public function isOperationSuccess()
    {
        return $this->success;
    }

    /**
     * Check if the contribution was successful.
     *
     * @return bool
     */
    public function isContributionSuccess() {
        return $this->contribution && $this->contribution->isSuccess();
    }
}