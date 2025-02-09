<?php

namespace MeSomb\Operation;

use MeSomb\Exception\PermissionDeniedException;
use MeSomb\Exception\ServiceNotFoundException;
use MeSomb\MeSomb;
use MeSomb\Util\RandomGenerator;
use PHPUnit\Framework\TestCase;

class FundraisingOperationTest extends TestCase
{
    private $applicationKey = 'fa78bded201b791712ee398c7ddfb8652669404f';
    private $accessKey = 'c6c40b76-8119-4e93-81bf-bfb55417b392';
    private $secretKey = 'fe8c2445-810f-4caa-95c9-778d51580163';

    protected function setUp(): void
    {
        MeSomb::$apiBase = 'http://127.0.0.1:8000';
    }

    public function testMakeContributeWithNotFoundService()
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

    public function testMakeContributeWithPermissionDenied()
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

    public function testMakeContributeWithInvalidAmount()
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

    public function testMakeContributeSuccess()
    {
        $payment = new FundraisingOperation($this->applicationKey, $this->accessKey, $this->secretKey);

        $response = $payment->makeContribution([
            'amount' => 100,
            'service' => 'MTN',
            'payer' => '670000000',
            'trxID' => '1',
            'full_name' => ['first_name' => 'John', 'last_name' => 'Doe'],
            'contact' => ['email' => 'contact@gmail.com', 'phone_number' => '+237677550203'],
        ]);
        $this->assertTrue($response->isOperationSuccess());
        $this->assertTrue($response->isContributionSuccess());
        $this->assertEquals('SUCCESS', $response->status);
        $this->assertEquals(98, $response->contribution->amount);
        $this->assertEquals(2, $response->contribution->fees);
        $this->assertEquals('237670000000', $response->contribution->b_party);
        $this->assertEquals('CM', $response->contribution->country);
        $this->assertEquals('XAF', $response->contribution->currency);
        $this->assertEquals('John', $response->contribution->contributor->first_name);
        $this->assertEquals('Doe', $response->contribution->contributor->last_name);
        $this->assertEquals('contact@gmail.com', $response->contribution->contributor->email);
        $this->assertEquals('+237677550203', $response->contribution->contributor->phone);
    }

    public function testMakeContributeAnonymousSuccess()
    {
        $payment = new FundraisingOperation($this->applicationKey, $this->accessKey, $this->secretKey);

        $response = $payment->makeContribution([
            'amount' => 100,
            'service' => 'MTN',
            'payer' => '670000000',
            'trxID' => '1',
            'anonymous' => true,
        ]);
        $this->assertTrue($response->isOperationSuccess());
        $this->assertTrue($response->isContributionSuccess());
        $this->assertEquals('SUCCESS', $response->status);
        $this->assertEquals(98, $response->contribution->amount);
        $this->assertEquals(2, $response->contribution->fees);
        $this->assertEquals('237670000000', $response->contribution->b_party);
        $this->assertEquals('CM', $response->contribution->country);
        $this->assertEquals('XAF', $response->contribution->currency);
        $this->assertNull($response->contribution->contributor);
    }

    public function testGetContributionSuccess()
    {
        $payment = new FundraisingOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $response = $payment->getContributions(['0685831f-4145-4352-ae81-155fec42c748']);
        $this->assertCount(1, $response);
        $this->assertEquals('0685831f-4145-4352-ae81-155fec42c748', $response[0]->pk);
    }

    public function testCheckContributionsSuccess()
    {
        $payment = new FundraisingOperation($this->applicationKey, $this->accessKey, $this->secretKey);
        $response = $payment->checkContributions(['0685831f-4145-4352-ae81-155fec42c748']);
        $this->assertCount(1, $response);
        $this->assertEquals('0685831f-4145-4352-ae81-155fec42c748', $response[0]->pk);
    }
}