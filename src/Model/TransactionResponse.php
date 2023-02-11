<?php

namespace MeSomb\Model;

class TransactionResponse
{
    /** @var bool|mixed */
    private $success;

    /** @var string|mixed|null  */
    private $message;

    /** @var string|mixed|null  */
    private $redirect;

    /** @var array|mixed */
    private $data;

    /** @var string|mixed|null  */
    private $reference;

    /** @var string|mixed  */
    private $status;

    public function __construct($data)
    {
        $this->success = $data['success'];
        $this->message = $data['message'];
        $this->redirect = $data['redirect'];
        $this->data = $data['transaction'];
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

    /**
     * @return bool|mixed
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @return mixed|string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed|string
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @return array|mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed|string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return mixed|string
     */
    public function getStatus()
    {
        return $this->status;
    }
}