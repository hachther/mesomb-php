<?php

namespace payment;

use MeSomb\Exception\PermissionDeniedException;
use MeSomb\Exception\ServiceNotFoundException;
use MeSomb\Operation\PaymentOperation;
use MeSomb\Settings;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    private string $applicationKey = '2bb525516ff374bb52545bf22ae4da7d655ba9fd';
    private string $accessKey = 'c6c40b76-8119-4e93-81bf-bfb55417b392';
    private string $secretKey = 'fe8c2445-810f-4caa-95c9-778d51580163';

    protected function setUp(): void
    {
        Settings::$HOST = 'http://127.0.0.1:8000';
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
}