<?php

namespace MeSomb\Operation;

use DateTime;
use MeSomb\Exception\InvalidClientRequestException;
use MeSomb\Exception\PermissionDeniedException;
use MeSomb\Exception\ServerException;
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
        $payment->makeCollect([
            'amount' => 5,
            'service' => 'MTN',
            'payer' => '670000000',
            'nonce' => $nonce,
        ]);
    }

    public function testMakeCollectWithPermissionDenied()
    {
        $payment = new PaymentOperation($this->applicationKey, "f" . substr($this->accessKey, 1), $this->secretKey);

        $this->expectException(PermissionDeniedException::class);
        $payment->makeCollect([
            'amount' => 5,
            'service' => 'MTN',
            'payer' => '670000000',
            'nonce' => RandomGenerator::nonce(),
            'trxID' => '1'
        ]);
    }

    public function testMakeCollectWithInvalidAmount()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $nonce = 'lkakdio90fsd8fsf';

        $this->expectExceptionCode('duplicated-request');
        $payment->makeCollect([
            'amount' => 5,
            'service' => 'MTN',
            'payer' => '670000000',
            'nonce' => $nonce,
        ]);
    }

    public function testMakeCollectSuccess()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);

        $response = $payment->makeCollect([
            'amount' => 100,
            'service' => 'MTN',
            'payer' => '670000000',
            'trxID' => '1'
        ]);
        $this->assertTrue($response->isOperationSuccess());
        $this->assertTrue($response->isTransactionSuccess());
        $this->assertEquals('SUCCESS', $response->status);
        $this->assertEquals(98, $response->transaction->amount);
        $this->assertEquals(2, $response->transaction->fees);
        $this->assertEquals('237670000000', $response->transaction->b_party);
        $this->assertEquals('CM', $response->transaction->country);
        $this->assertEquals('XAF', $response->transaction->currency);
        $this->assertEquals('1', $response->transaction->reference);
    }

    public function testMakeCollectPending()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);

        $response = $payment->makeCollect([
            'amount' => 100,
            'service' => 'MTN',
            'payer' => '670000000',
            'mode' => 'asynchronous'
        ]);
        $this->assertTrue($response->isOperationSuccess());
        $this->assertFalse($response->isTransactionSuccess());
        $this->assertEquals('PENDING', $response->transaction->status);
    }

    /**
     * @throws ServiceNotFoundException
     * @throws InvalidClientRequestException
     * @throws PermissionDeniedException
     * @throws ServerException
     */
    public function testMakeCollectWithProductSuccess()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);

        $response = $payment->makeCollect([
            'amount' => 100,
            'service' => 'MTN',
            'payer' => '670000000',
            'trxID' => '1',
            'products' => [
                [
                    'id' => 'SKU001',
                    'name' => 'Sac a Dos',
                    'category' => 'Sac'
                ]
            ],
            'customer' => [
                'phone' => '+237677550439',
                'email' => 'fisher.bank@gmail.com',
                'first_name' => 'Fisher',
                'last_name' => 'BANK'
            ],
            'location' => [
                'town' => 'Douala',
                'country' => 'Cameroun'
            ]
        ]);
        $this->assertTrue($response->isOperationSuccess());
        $this->assertTrue($response->isTransactionSuccess());
        $this->assertEquals('SUCCESS', $response->status);
        $this->assertEquals(98, $response->transaction->amount);
        $this->assertEquals(2, $response->transaction->fees);
        $this->assertEquals('237670000000', $response->transaction->b_party);
        $this->assertEquals('CM', $response->transaction->country);
        $this->assertEquals('XAF', $response->transaction->currency);
        $this->assertEquals('1', $response->transaction->reference);
        $this->assertEquals('+237677550439', $response->transaction->customer->phone);
        $this->assertEquals('fisher.bank@gmail.com', $response->transaction->customer->email);
        $this->assertEquals('Fisher', $response->transaction->customer->first_name);
        $this->assertEquals('BANK', $response->transaction->customer->last_name);
        $this->assertEquals('Douala', $response->transaction->location->town);
        $this->assertEquals('Cameroun', $response->transaction->location->country);
        $this->assertCount(1, $response->transaction->products);
    }

    public function testMakeDepositWithNotFoundService()
    {
        $payment = new PaymentOperation($this->applicationKey . "f", $this->accessKey, $this->secretKey);
        $nonce = RandomGenerator::nonce();

        $this->expectException(ServiceNotFoundException::class);
        $payment->makeDeposit([
            'amount' => 5,
            'service' => 'MTN',
            'receiver' => '670000000',
            'nonce' => $nonce,
        ]);
    }

    public function testMakeDepositWithPermissionDenied()
    {
        $payment = new PaymentOperation($this->applicationKey, "f" . substr($this->accessKey, 1), $this->secretKey);
        $nonce = RandomGenerator::nonce();

        $this->expectException(PermissionDeniedException::class);
        $payment->makeDeposit([
            'amount' => 5,
            'service' => 'MTN',
            'receiver' => '670000000',
            'nonce' => $nonce,
        ]);
    }

    public function testMakeDepositSuccess()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);

        $response = $payment->makeDeposit([
            'amount' => 100,
            'service' => 'MTN',
            'receiver' => '670000000',
            'trxID' => '1'
        ]);
        $this->assertTrue($response->isOperationSuccess());
        $this->assertTrue($response->isTransactionSuccess());
        $this->assertEquals('SUCCESS', $response->status);
        $this->assertEquals(100, $response->transaction->amount);
        $this->assertEquals(1.01, $response->transaction->fees);
        $this->assertEquals('237670000000', $response->transaction->b_party);
        $this->assertEquals('CM', $response->transaction->country);
        $this->assertEquals('XAF', $response->transaction->currency);
        $this->assertEquals('1', $response->transaction->reference);
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
        $this->assertEquals('Meudocta Shop', $application->name);
        $this->assertEquals(['CM', 'NE'], $application->countries);
    }

    public function testGetTransactionsWithNotFoundService()
    {
        $payment = new PaymentOperation($this->applicationKey . "f", $this->accessKey, $this->secretKey);
        $this->expectException(ServiceNotFoundException::class);
        $payment->getTransactions(['c6c40b76-8119-4e93-81bf-bfb55417b392', 'c6c40b76-8119-4e93-81bf-bfb55417b393']);
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
        $response = $payment->getTransactions(['a483a9e8-51d7-44c9-875b-1305b1801274']);
        $this->assertCount(1, $response);
        $this->assertEquals('a483a9e8-51d7-44c9-875b-1305b1801274', $response[0]->pk);
    }

    public function testCheckTransactionsWithNotFoundService()
    {
        $payment = new PaymentOperation($this->applicationKey . "f", $this->accessKey, $this->secretKey);
        $this->expectException(ServiceNotFoundException::class);
        $payment->checkTransactions(['a483a9e8-51d7-44c9-875b-1305b1801274']);
    }

    public function testCheckTransactionsWithPermissionDenied()
    {
        $payment = new PaymentOperation($this->applicationKey, "f" . substr($this->accessKey, 1), $this->secretKey);
        $this->expectException(PermissionDeniedException::class);
        $payment->checkTransactions(['a483a9e8-51d7-44c9-875b-1305b1801274']);
    }

    public function testCheckTransactionsSuccess()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $response = $payment->checkTransactions(['a483a9e8-51d7-44c9-875b-1305b1801274']);
        $this->assertCount(1, $response);
        $this->assertEquals('a483a9e8-51d7-44c9-875b-1305b1801274', $response[0]->pk);
    }

    public function testRefundTransactionSuccess()
    {
        $payment = new PaymentOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $response = $payment->refundTransaction('a483a9e8-51d7-44c9-875b-1305b1801274');
        $this->assertCount(1, $response);
        $this->assertEquals('a483a9e8-51d7-44c9-875b-1305b1801274', $response[0]->pk);
    }
}