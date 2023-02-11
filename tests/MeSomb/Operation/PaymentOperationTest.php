<?php

namespace MeSomb\Operation;

use DateTime;
use MeSomb\Exception\PermissionDeniedException;
use MeSomb\Exception\ServiceNotFoundException;
use MeSomb\MeSomb;
use MeSomb\Util\RandomGenerator;
use PHPUnit\Framework\TestCase;

class PaymentOperationTest extends TestCase
{
    private $applicationKey = '2bb525516ff374bb52545bf22ae4da7d655ba9fd';
    private $accessKey = 'c6c40b76-8119-4e93-81bf-bfb55417b392';
    private $secretKey = 'fe8c2445-810f-4caa-95c9-778d51580163';

    protected function setUp(): void
    {
        MeSomb::$apiBase = 'http://127.0.0.1:8000';
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
        $nonce = RandomGenerator::nonce();

        $response = $payment->makeCollect(100, 'MTN', '677550203', new DateTime(), $nonce);
        $this->assertTrue($response->isOperationSuccess());
        $this->assertTrue($response->isTransactionSuccess());
    }

    public function testMakeCollectPending()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $nonce = RandomGenerator::nonce();

        $response = $payment->makeCollect(100, 'MTN', '677550203', new DateTime(), $nonce, null, 'CM', 'XAF', true, 'asynchronous');
        $this->assertTrue($response->isOperationSuccess());
        $this->assertFalse($response->isTransactionSuccess());
    }

    public function testMakeCollectWithProductSuccess()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $nonce = RandomGenerator::nonce();

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

    public function testMakeDepositWithNotFoundService()
    {
        $payment = new PaymentOperation($this->applicationKey . "f", $this->accessKey, $this->secretKey);
        $nonce = RandomGenerator::nonce();

        $this->expectException(ServiceNotFoundException::class);
        $payment->makeDeposit(5, 'MTN', '677550203', new DateTime(), $nonce);
    }

    public function testMakeDepositWithPermissionDenied()
    {
        $payment = new PaymentOperation($this->applicationKey, "f" . substr($this->accessKey, 1), $this->secretKey);
        $nonce = RandomGenerator::nonce();

        $this->expectException(PermissionDeniedException::class);
        $payment->makeDeposit(5, 'MTN', '677550203', new DateTime(), $nonce);
    }

    public function testMakeDepositSuccess()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $nonce = RandomGenerator::nonce();

        $response = $payment->makeDeposit(100, 'MTN', '677550203', new DateTime(), $nonce);
        $this->assertTrue($response->isOperationSuccess());
        $this->assertTrue($response->isTransactionSuccess());
    }

    public function testGetStatusWithNotFoundService()
    {
        $payment = new PaymentOperation($this->applicationKey . "f", $this->accessKey, $this->secretKey);
        $this->expectException(ServiceNotFoundException::class);
        $payment->getStatus();
    }

    public function testGetStatusWithPermissionDenied()
    {
        $payment = new PaymentOperation($this->applicationKey, "f" . substr($this->accessKey, 1), $this->secretKey);
        $this->expectException(PermissionDeniedException::class);
        $payment->getStatus();
    }

    public function testGetStatusSuccess()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $application = $payment->getStatus();
        $this->assertEquals($application->getName(), 'Meudocta Shop');
    }

    public function testUnsetWhitelistIPs()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $application = $payment->updateSecurity('whitelist_ips', 'UNSET');
        $this->assertNull($application->getSecurityField('whitelist_ips'));
    }

    public function testUnsetBlacklistReceivers()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $application = $payment->updateSecurity('blacklist_receivers', 'UNSET');
        $this->assertNull($application->getSecurityField('blacklist_receivers'));
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