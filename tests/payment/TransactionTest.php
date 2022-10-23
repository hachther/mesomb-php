<?php

namespace payment;

use MeSomb\Exception\PermissionDeniedException;
use MeSomb\Exception\ServiceNotFoundException;
use MeSomb\Operation\PaymentOperation;
use MeSomb\Settings;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    private string $applicationKey = '2bb525516ff374bb52545bf22ae4da7d655ba9fd';
    private string $accessKey = 'c6c40b76-8119-4e93-81bf-bfb55417b392';
    private string $secretKey = 'fe8c2445-810f-4caa-95c9-778d51580163';

    protected function setUp(): void
    {
        Settings::$HOST = 'http://127.0.0.1:8000';
    }

    public function testGetTransactionsWithNotFoundService()
    {
        $payment = new PaymentOperation($this->applicationKey . "f", $this->accessKey, $this->secretKey);
        $this->expectException(ServiceNotFoundException::class);
        $payment->getTransactions(['c6c40b76-8119-4e93-81bf-bfb55417b392']);
    }

    public function testGetTransactionsWithPermissionDenied()
    {
        $payment = new PaymentOperation($this->applicationKey, "f" . substr($this->accessKey, 1), $this->secretKey);
        $this->expectException(PermissionDeniedException::class);
        $payment->getTransactions(['c6c40b76-8119-4e93-81bf-bfb55417b392']);
    }

    public function testGetTransactionsSuccess()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $response = $payment->getTransactions(['9886f099-dee2-4eaa-9039-e92b2ee33353']);
        $this->assertEquals(1, count($response));
    }
}