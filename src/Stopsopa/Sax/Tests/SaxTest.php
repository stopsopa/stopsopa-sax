<?php

namespace Stopsopa\Sax\Tests;

use Stopsopa\Sax\Sax;

class SaxTest extends \PHPUnit_Framework_TestCase {
    protected $xml = <<<end
<?xml version="1.0" encoding="UTF-8"?>
<note>
	<to>Tove</to>
	<from>Jani</from>
	<heading>Reminder</heading>
	<body>Don't forget me this weekend!</body>
</note>
end;

//    public function testTest() {
//        $i = new Sax($this->xml);
//        $this->assertEquals('value', $i->doSomething());
//    }
    public function testTest() {
        $i  = new Sax($this->xml);

        foreach ($i as $d) {
            fwrite(STDOUT, '< '.$d . " >");
        }
        $this->assertEquals('', '');
    }
}