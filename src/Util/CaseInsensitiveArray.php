<?php

namespace MeSomb\Util;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use function array_change_key_case;
use function count;
use function is_string;
use function strtolower;

/**
 * CaseInsensitiveArray is an array-like class that ignores case for keys.
 *
 * It is used to store HTTP headers. Per RFC 2616, section 4.2:
 * Each header field consists of a name followed by a colon (":") and the field value. Field names
 * are case-insensitive.
 *
 * In the context of mesomb-php, this is useful because the API will return headers with different
 * case depending on whether HTTP/2 is used or not (with HTTP/2, headers are always in lowercase).
 */
class CaseInsensitiveArray implements ArrayAccess, Countable, IteratorAggregate
{
    private $container;

    public function __construct($initial_array = [])
    {
        $this->container = array_change_key_case($initial_array);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->container);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->container);
    }

    /**
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $offset = static::maybeLowercase($offset);
        if (null === $offset) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        $offset = static::maybeLowercase($offset);

        return isset($this->container[$offset]);
    }

    /**
     * @param $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $offset = static::maybeLowercase($offset);
        unset($this->container[$offset]);
    }

    /**
     * @param $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        $offset = static::maybeLowercase($offset);

        return $this->container[$offset] ?? null;
    }

    private static function maybeLowercase($v): mixed
    {
        if (is_string($v)) {
            return strtolower($v);
        }

        return $v;
    }
}
