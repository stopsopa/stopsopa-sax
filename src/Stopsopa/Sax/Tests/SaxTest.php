<?php

namespace Stopsopa\Sax\Tests;

use Stopsopa\Sax\Sax;

class SaxTest extends \PHPUnit_Framework_TestCase {

    public function testSimpleXml() {
        $data = <<<eos
      <?xml version="1.0" encoding="UTF-8"?>
<note>
	<to>Tove</to>
	<from>Jani</from>
	<heading>Remiąćęńółnder</heading>
	<bo ą ąśżźćęół dy>Don't
	forget me this weekend!</body>
</note>

<koniec>

eos;
        $types = json_decode('["s","t","s","t","s","t","d","t","s","t","d","t","s","t","d","t","s","t","d","t","s","t","s","t","s"]', true);

        $k  = new Sax($data, array(
            'mode' => Sax::MODE_STRING
        ));

        $t = '';

        $i = 0;
        foreach ($k as $d) {
            $t .= $d['data'];
            $this->assertSame($d['type'], $types[$i]);
            $i += 1;
        }

        // compare entire initial string with mounted through iteration
        $this->assertSame($data, $t);
    }
}