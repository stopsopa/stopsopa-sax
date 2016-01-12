<?php

namespace Stopsopa\Sax\Tests\Lib;

use Stopsopa\Sax\Lib\Sax;

class SaxTest extends \PHPUnit_Framework_TestCase {
    public function testTest() {
        $i = new Sax();
        $this->assertEquals('value', $i->doSomething());
    }
}