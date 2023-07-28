<?php

namespace MeSomb\Model;

class TransactionResponse
{
    /** @var bool|mixed */
    public $success;

    /** @var string|mixed|null  */
    public $message;

    /** @var string|mixed|null  */
    public $redirect;

    /** @var Transaction */
    public $transaction;

    /** @var string|mixed|null  */
    public $reference;

    /** @var string|mixed  */
    public $status;

    public function __construct($data)
    {
        $this->success = $data['success'];
        $this->message = $data['message'];
        $this->redirect = $data['redirect'];
        $this->transaction = new Transaction($data['transaction']);
        $this->reference = $data['reference'];
        $this->status = $data['status'];
    }

    public function isOperationSuccess()
    {
        return $this->success;
    }

    public function isTransactionSuccess() {
        return $this->success && $this->status == 'SUCCESS';
    }
}