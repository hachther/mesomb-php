<?php

namespace MeSomb\Model;

class TransactionResponse
{
    private bool $success;
    private ?string $message;
    private ?string $redirect;
    private array $data;
    private ?string $reference;
    private string $status;

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
    public function getSuccess(): mixed
    {
        return $this->success;
    }

    /**
     * @return mixed|string
     */
    public function getMessage(): mixed
    {
        return $this->message;
    }

    /**
     * @return mixed|string
     */
    public function getRedirect(): mixed
    {
        return $this->redirect;
    }

    /**
     * @return array|mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * @return mixed|string
     */
    public function getReference(): mixed
    {
        return $this->reference;
    }

    /**
     * @return mixed|string
     */
    public function getStatus(): mixed
    {
        return $this->status;
    }
}