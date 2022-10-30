<?php

namespace payment;

use DateTime;
use MeSomb\Exception\PermissionDeniedException;
use MeSomb\Exception\ServiceNotFoundException;
use MeSomb\Operation\PaymentOperation;
use MeSomb\Settings;
use MeSomb\Signature;
use PHPUnit\Framework\TestCase;

class CollectTest extends TestCase
{
    private string $applicationKey = '2bb525516ff374bb52545bf22ae4da7d655ba9fd';
    private string $accessKey = 'c6c40b76-8119-4e93-81bf-bfb55417b392';
    private string $secretKey = 'fe8c2445-810f-4caa-95c9-778d51580163';

    protected function setUp(): void
    {
        Settings::$HOST = 'http://127.0.0.1:8000';
    }

    public function testMakeCollectWithNotFoundService()
    {
        $payment = new PaymentOperation($this->applicationKey . "f", $this->accessKey, $this->secretKey);
        $nonce = 'lkakdio90fsd8fsf';

        $this->expectException(ServiceNotFoundException::class);
        $payment->makeCollect(5, 'MTN', '677550203', new DateTime(), $nonce);
    }

    public function testMakeCollectWithPermissionDenied()
    {
        $payment = new PaymentOperation($this->applicationKey, "f" . substr($this->accessKey, 1), $this->secretKey);
        $nonce = 'lkakdio90fsd8fsf';

        $this->expectException(PermissionDeniedException::class);
        $payment->makeCollect(5, 'MTN', '677550203', new DateTime(), $nonce);
    }

    public function testMakeCollectWithInvalidAmount()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $nonce = 'lkakdio90fsd8fsf';

        $this->expectExceptionCode('invalid-amount');
        $payment->makeCollect(5, 'MTN', '677550203', new DateTime(), $nonce);
    }

    public function testMakeCollectSuccess()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $nonce = Signature::nonceGenerator();

        $response = $payment->makeCollect(100, 'MTN', '677550203', new DateTime(), $nonce);
        $this->assertTrue($response->isOperationSuccess());
        $this->assertTrue($response->isTransactionSuccess());
    }

    public function testMakeCollectPending()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $nonce = Signature::nonceGenerator();

        $response = $payment->makeCollect(100, 'MTN', '677550203', new DateTime(), $nonce, null, 'CM', 'XAF', true, 'asynchronous');
        $this->assertTrue($response->isOperationSuccess());
        $this->assertFalse($response->isTransactionSuccess());
    }

    public function testMakeCollectWithProductSuccess()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $nonce = Signature::nonceGenerator();

        $products = [
            [
                'id' => '1',
                'name' => 'Sac a Dos',
                'quantity' => 1,
                'amount' => 100
            ]
        ];

        $response = $payment->makeCollect(100, 'MTN', '677550203', new DateTime(), $nonce, null, 'CM', 'XAF', true, 'synchronous', false, null, null, $products);
        $this->assertTrue($response->isOperationSuccess());
        $this->assertTrue($response->isTransactionSuccess());
    }
}
