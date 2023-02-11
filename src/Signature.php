<?php

namespace MeSomb;

use DateTime;

class Signature
{
    /**
     * @param string $service service to use can be payment, wallet ... (the list is provide by MeSomb)
     * @param string $method HTTP method (GET, POST, PUT, PATCH, DELETE...)
     * @param string $url the full url of the request with query element https://mesomb.hachther.com/path/to/ressource?highlight=params#url-parsing
     * @param DateTime $date Datetime of the request
     * @param string $nonce Unique string generated for each request sent to MeSomb
     * @param array $credentials dict containing key => value for the credential provided by MeSOmb. {'access' => access_key, 'secret' => secret_key}
     * @param array $headers Extra HTTP header to use in the signature
     * @param array|null $body The dict containing the body you send in your request body
     * @return string Authorization to put in the header
     */
    public static function signRequest($service, $method, $url, DateTime $date, $nonce, array $credentials, array $headers = [], array $body = null)
    {
        $algorithm = MeSomb::$algorithm;
        $parse = parse_url($url);
        $canonicalQuery = isset($parse['query']) ? $parse['query'] : '';

        $timestamp = $date->getTimestamp();

        if (!isset($headers)) {
            $headers = [];
        }
        $headers['host'] = $parse['scheme']."://".$parse['host'].(isset($parse['port']) ? ":".$parse['port'] : '');
        $headers['x-mesomb-date'] = $timestamp;
        $headers['x-mesomb-nonce'] = $nonce;
        ksort($headers);
        $callback = function ($k, $v) {
            return strtolower($k) . ":" . $v;
        };
        $canonicalHeaders = implode("\n", array_map($callback, array_keys($headers), array_values($headers)));

        if (!isset($body)) {
            $body = "{}";
        } else {
            $body = json_encode($body, JSON_UNESCAPED_SLASHES);
        }
        $payloadHash = sha1($body);

        $signedHeaders = implode(";", array_keys($headers));

        $path = implode("/", array_map("rawurlencode", explode("/", $parse['path'])));
        $canonicalRequest = $method."\n".$path."\n".$canonicalQuery."\n".$canonicalHeaders."\n".$signedHeaders."\n".$payloadHash;

        $scope = $date->format("Ymd")."/".$service."/mesomb_request";
        $stringToSign = $algorithm."\n".$timestamp."\n".$scope."\n".sha1($canonicalRequest);

        $signature = hash_hmac('sha1', $stringToSign, $credentials['secretKey'], false);
        $accessKey = $credentials['accessKey'];

        return "$algorithm Credential=$accessKey/$scope, SignedHeaders=$signedHeaders, Signature=$signature";
    }
}
