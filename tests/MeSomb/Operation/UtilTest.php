<?php

namespace MeSomb\Operation;

use MeSomb\Signature;
use MeSomb\Util\Util;
use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    public function testOperatorDetector()
    {
        $this->assertEquals('MTN', Util::detectOperator('677559230'));
        $this->assertEquals('MTN', Util::detectOperator('237677559230'));
        $this->assertEquals('ORANGE', Util::detectOperator('690090980'));
        $this->assertEquals('ORANGE', Util::detectOperator('237690090980'));
    }
}
