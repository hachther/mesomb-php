<?php

namespace Hachther\MeSomb;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client as RestClient;

class Client
{
    private $application = null;
    private $fees = true;
    private $language = 'fr';

    /**
     * Client constructor.
     * @param string $application : key from MeSomb
     * @param bool $fees : defined if MeSomb fees are already included in the amount
     * @param string $language : defined language to send to MeSomb API
     */
    public function __construct($application, $fees = true, $language = 'fr')
    {
        $this->application = $application;
        $this->fees = $fees;
        $this->language = $language;
    }

    /**
     * @param string $payer : phoneNumber of payer format 237600000000
     * @param int $amount : amount of the transaction
     * @param string $service : operator of the payer ORANGE or MTN
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function makePayment($payer, $amount, $service)
    {
        $client = new RestClient();
        try {
            $url = 'https://mesomb.hachther.com/api/v1.0/payment/online/';
            $response = $client->request('POST', $url, [
                'json' => [
                    'amount' => $amount,
                    'payer' => $payer,
                    'fees' => $this->fees,
                    'service' => $service
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-MeSomb-Application' => $this->application,
                    'Accept-Language' => $this->language
                ]
            ]);
            $stringBody = (string)$response->getBody();
            $data = json_decode($stringBody);
            $result = [
                'status' => $data->status,
                'message' => $data->message
            ];
            if (isset($data->transaction)) {
                $result['transaction'] = $data->transaction;
            }
            return $result;
        } catch (RequestException $e) {
            $result = [
                'status' => 'FAIL'
            ];
            if ($e->hasResponse()) {
                $data = json_decode($e->getResponse()->getBody()->getContents());
                $result['code'] = $data->code;
                $result['message'] = $data->detail;
            }
            return $result;
        }
    }
}