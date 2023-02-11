<?php

namespace MeSomb\Util;

/**
 * A basic random generator. This is in a separate class so we the generator
 * can be injected as a dependency and replaced with a mock in tests.
 */
class RandomGenerator
{
    /**
     * Returns a random value between 0 and $max.
     *
     * @param float $max (optional)
     *
     * @return float
     */
    public function randFloat($max = 1.0)
    {
        return \mt_rand() / \mt_getrandmax() * $max;
    }

    /**
     * Returns a v4 UUID.
     *
     * @return string
     */
    public function uuid()
    {
        $arr = \array_values(\unpack('N1a/n4b/N1c', \openssl_random_pseudo_bytes(16)));
        $arr[2] = ($arr[2] & 0x0FFF) | 0x4000;
        $arr[3] = ($arr[3] & 0x3FFF) | 0x8000;

        return \vsprintf('%08x-%04x-%04x-%04x-%04x%08x', $arr);
    }

    /**
     * Generate a random string by the length
     *
     * @param int $length of the random string to generate
     * @return string
     */
    public static function nonce($length = null) {
        if (is_null($length)) {
            $length = 40;
        }
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
