<?php

namespace MeSomb;

use DateTimeZone;
use PHPUnit\Framework\TestCase;

class SignatureTest extends TestCase
{
    public function testSignatureWithGet()
    {
        $date = new \DateTime();
        $date->setTimestamp(1673827200);
        $signature = Signature::signRequest('payment', 'GET', 'http://127.0.0.1:8000/en/api/v1.1/payment/collect/', $date, 'fihser', ['accessKey' => 'c6c40b76-8119-4e93-81bf-bfb55417b392', 'secretKey' => 'fe8c2445-810f-4caa-95c9-778d51580163']);
        $this->assertEquals('HMAC-SHA1 Credential=c6c40b76-8119-4e93-81bf-bfb55417b392/20230116/payment/mesomb_request, SignedHeaders=host;x-mesomb-date;x-mesomb-nonce, Signature=92866ff78427c739c1d48c9223a6133cde46ab5d', $signature);
    }

    public function testSignatureWithPost()
    {
        $date = new \DateTime();
        $date->setTimestamp(1673827200);
        $signature = Signature::signRequest('payment', 'POST', 'http://127.0.0.1:8000/en/api/v1.1/payment/collect/', $date, 'fihser', ['accessKey' => 'c6c40b76-8119-4e93-81bf-bfb55417b392', 'secretKey' => 'fe8c2445-810f-4caa-95c9-778d51580163'], [], [
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
        $this->assertEquals('HMAC-SHA1 Credential=c6c40b76-8119-4e93-81bf-bfb55417b392/20230116/payment/mesomb_request, SignedHeaders=host;x-mesomb-date;x-mesomb-nonce, Signature=9a34bb874cd826d785b5d744cc39d4e5776fd999', $signature);
    }
}
