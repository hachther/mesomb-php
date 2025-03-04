<?php

namespace MeSomb\Model;

/**
 * Class Transaction
 * @package MeSomb\Model
 *
 * @property bool $success - Indicates if the operation was successful.
 * @property string|null $message - Message associated with the transaction response.
 * @property string|null $redirect - URL to redirect if needed.
 * @property Transaction $transaction - The transaction details.
 * @property string|null $reference - Reference ID of the transaction.
 * @property string $status - Status of the transaction.
 */
class TransactionResponse
{
    /** @var bool */
    public $success;

    /** @var string|null  */
    public $message;

    /** @var string|null  */
    public $redirect;

    /** @var Transaction */
    public $transaction;

    /** @var string|null  */
    public $reference;

    /** @var string  */
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
     * Check if the transaction was successful.
     *
     * @return bool
     */
    public function isTransactionSuccess() {
        return $this->transaction && $this->transaction->isSuccess();
    }
}