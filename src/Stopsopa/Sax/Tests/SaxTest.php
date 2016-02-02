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

        $sax = new Sax($data, array(
            'mode' => Sax::MODE_STRING
        ));

        $t = '';
        $i = 0;
        foreach ($sax as $d) {
            $t .= $d['raw'];
            $this->assertSame($d['type'], $types[$i]);
            $i += 1;
        }
        // compare entire initial string with mounted through iteration
        $this->assertSame($data, $t);

        // once again to check reset() method
        $t = '';
        $i = 0;
        foreach ($sax as $d) {
            $t .= $d['raw'];
            $this->assertSame($d['type'], $types[$i]);
            $i += 1;
        }
        // compare entire initial string with mounted through iteration
        $this->assertSame($data, $t);
    }
    public function testComplex() {

        $check = <<<eos
[{"type":"t","raw":"<?xml version=\"1.0\" encoding=\"UTF-8\"?>","data":{"type":"opening","name":"?xml","attr":{"version":"1.0","encoding":"UTF-8"}},"offset":0},{"type":"s","raw":"\r\n","offset":38},{"type":"t","raw":"<note>","data":{"type":"opening","name":"note","attr":[]},"offset":40},{"type":"t","raw":"<div>","data":{"type":"opening","name":"div","attr":[]},"offset":46},{"type":"s","raw":"\r\n            ","offset":51},{"type":"c","raw":"<![CDATA[\r\nYou will see this in the document\r\nand can use reserved characters like\r\n< > & \"\r\n]]>","data":"\r\nYou will see this in the document\r\nand can use reserved characters like\r\n< > & \"\r\n","offset":65},{"type":"s","raw":"\r\n\r\n","offset":161},{"type":"t","raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":165},{"type":"s","raw":"\r\n        ","offset":171},{"type":"t","raw":"<span test=\"raz\" data-dwa=\"entity\" data-dwa=\"value2\" class=\"red blue\">","data":{"type":"opening","name":"span","attr":{"test":"raz","data-dwa":["entity","value2"],"class":"red blue"}},"offset":181},{"type":"d","raw":"test span","offset":251},{"type":"t","raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":260},{"type":"s","raw":"\r\n        ","offset":267},{"type":"t","raw":"<div>","data":{"type":"opening","name":"div","attr":[]},"offset":277},{"type":"s","raw":"\r\n\r\n            ","offset":282},{"type":"c","raw":"<![CDATA[\r\nYou will see this in the document\r\nand can use reserved characters <co\u015b tam co\u015b tam>like\r\n<b>bold<\/b>\r\n< > & \"\r\n]]>","data":"\r\nYou will see this in the document\r\nand can use reserved characters <co\u015b tam co\u015b tam>like\r\n<b>bold<\/b>\r\n< > & \"\r\n","offset":298},{"type":"s","raw":"\r\n        ","offset":424},{"type":"t","raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":434},{"type":"s","raw":"\r\n        ","offset":440},{"type":"t","raw":"<div:test>","data":{"type":"opening","name":"div:test","attr":[]},"offset":450},{"type":"s","raw":"\r\n            ","offset":460},{"type":"c","raw":"<![CDATA[]]>","data":"","offset":474},{"type":"s","raw":"\r\n        ","offset":486},{"type":"t","raw":"<\/div:test>","data":{"type":"closing","name":"div:test"},"offset":496},{"type":"s","raw":"\r\n        ","offset":507},{"type":"t","raw":"<span \/>","data":{"type":"empty","name":"span","attr":[]},"offset":517},{"type":"s","raw":"\r\n        ","offset":525},{"type":"t","raw":"<span>","data":{"type":"opening","name":"span","attr":[]},"offset":535},{"type":"d","raw":"test","offset":541},{"type":"t","raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":545},{"type":"s","raw":"\r\n        ","offset":552},{"type":"t","raw":"<span:split te:st=\"value'valuepart2\" te:st2\/>","data":{"type":"empty","name":"span:split","attr":{"te:st":"value'valuepart2","te:st2":null}},"offset":562},{"type":"s","raw":"\r\n        ","offset":607},{"type":"t","raw":"<span:split te:st=\"value'valuepart2\" te:st2 \/>","data":{"type":"empty","name":"span:split","attr":{"te:st":"value'valuepart2","te:st2":null}},"offset":617},{"type":"s","raw":"\r\n        ","offset":663},{"type":"t","raw":"<span:split2>","data":{"type":"opening","name":"span:split2","attr":[]},"offset":673},{"type":"t","raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":686},{"type":"s","raw":"\r\n        ","offset":699},{"type":"t","raw":"<span:split2>","data":{"type":"opening","name":"span:split2","attr":[]},"offset":709},{"type":"d","raw":"some\r\n        data","offset":722},{"type":"t","raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":740},{"type":"s","raw":"\r\n        ","offset":753},{"type":"t","raw":"<span:split2>","data":{"type":"opening","name":"span:split2","attr":[]},"offset":763},{"type":"c","raw":"<![CDATA[<span>cdata<\/span>]]>","data":"<span>cdata<\/span>","offset":776},{"type":"t","raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":806},{"type":"s","raw":"\r\n        ","offset":819},{"type":"t","raw":"<span:split2>","data":{"type":"opening","name":"span:split2","attr":[]},"offset":829},{"type":"s","raw":"\r\n        ","offset":842},{"type":"c","raw":"<![CDATA[\r\n        <span>cdata<\/span>\r\n        ]]>","data":"\r\n        <span>cdata<\/span>\r\n        ","offset":852},{"type":"s","raw":"\r\n        ","offset":902},{"type":"t","raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":912},{"type":"s","raw":"\r\n        ","offset":925},{"type":"t","raw":"<div>","data":{"type":"opening","name":"div","attr":[]},"offset":935},{"type":"s","raw":"\r\n            ","offset":940},{"type":"c","raw":"<![CDATA[test ]>]]>","data":"test ]>","offset":954},{"type":"s","raw":"\r\n\r\n        ","offset":973},{"type":"t","raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":985},{"type":"s","raw":"\r\n        ","offset":991},{"type":"t","raw":"<span>","data":{"type":"opening","name":"span","attr":[]},"offset":1001},{"type":"d","raw":"span data","offset":1007},{"type":"t","raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":1016},{"type":"s","raw":"\r\n        ","offset":1023},{"type":"t","raw":"<div>","data":{"type":"opening","name":"div","attr":[]},"offset":1033},{"type":"s","raw":"\r\n            ","offset":1038},{"type":"c","raw":"<![CDATA[<span>test<\/span>]]>","data":"<span>test<\/span>","offset":1052},{"type":"s","raw":"\r\n\r\n        ","offset":1081},{"type":"t","raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":1093},{"type":"s","raw":"\r\n        ","offset":1099},{"type":"t","raw":"<span test=\"raz\" data-dwa=\"entity\" data-dwa=\"value2\" class=\"red blue\">","data":{"type":"opening","name":"span","attr":{"test":"raz","data-dwa":["entity","value2"],"class":"red blue"}},"offset":1109},{"type":"d","raw":"test span","offset":1179},{"type":"t","raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":1188},{"type":"s","raw":"\r\n        ","offset":1195},{"type":"t","raw":"<span src=\"image\">","data":{"type":"opening","name":"span","attr":{"src":"image"}},"offset":1205},{"type":"s","raw":"\r\n        ","offset":1223},{"type":"t","raw":"<span test=\"raz\" data-dwa=\"entity\" data-dwa=\"value2\" class=\"red blue\"\/>","data":{"type":"empty","name":"span","attr":{"test":"raz","data-dwa":["entity","value2"],"class":"red blue"}},"offset":1233},{"type":"d","raw":"test span","offset":1304},{"type":"t","raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":1313},{"type":"s","raw":"\r\n    ","offset":1320},{"type":"t","raw":"<\/test>","data":{"type":"closing","name":"test"},"offset":1326},{"type":"s","raw":"\r\n","offset":1333},{"type":"t","raw":"<\/note>","data":{"type":"closing","name":"note"},"offset":1335}]
eos;

        $check = json_decode($check, true);

        fwrite(STDOUT, $this->_var_dump('json_decode'));
        fwrite(STDOUT, $this->_var_dump($check));
        die('koniec');

        $data = <<<eos
<?xml version="1.0" encoding="UTF-8"?>
<note><div>
            <![CDATA[
You will see this in the document
and can use reserved characters like
< > & "
]]>

</div>
        <span test="raz" data-dwa="entity" data-dwa="value2" class="red blue">test span</span>
        <div>

            <![CDATA[
You will see this in the document
and can use reserved characters <coś tam coś tam>like
<b>bold</b>
< > & "
]]>
        </div>
        <div:test>
            <![CDATA[]]>
        </div:test>
        <span />
        <span>test</span>
        <span:split te:st="value'valuepart2" te:st2/>
        <span:split te:st="value'valuepart2" te:st2 />
        <span:split2></span:split>
        <span:split2>some
        data</span:split>
        <span:split2><![CDATA[<span>cdata</span>]]></span:split>
        <span:split2>
        <![CDATA[
        <span>cdata</span>
        ]]>
        </span:split>
        <div>
            <![CDATA[test ]>]]>

        </div>
        <span>span data</span>
        <div>
            <![CDATA[<span>test</span>]]>

        </div>
        <span test="raz" data-dwa="entity" data-dwa="value2" class="red blue">test span</span>
        <span src="image">
        <span test="raz" data-dwa="entity" data-dwa="value2" class="red blue"/>test span</span>
    </test>
</note>
eos;

        $sax  = new Sax($data, array(
            'mode' => Sax::MODE_STRING
        ));

        $this->_testComplexHelper($sax, $data, $check);

        // check reset
        $this->_testComplexHelper($sax, $data, $check);
    }
    protected function _testComplexHelper(Sax $sax, $data, $check) {

        $t = '';
        $i = 0;
        foreach ($sax as $d) {
            $t .= $d['raw'];

            $p = $this->_var_dump($i, true);
            fwrite(STDOUT, "\n\n- 1 -> $p\n");
            $p = $this->_var_dump($d, true);
            fwrite(STDOUT, "\n\n- 2 -> $p\n");
            $p = $this->_var_dump($check[$i], true);
            fwrite(STDOUT, "\n\n- 3 -> $p\n");

            $this->assertSame(json_encode($d), json_encode($check[$i]));
            $i += 1;

            // offset test
            $c1 = mb_substr($data, $d['offset'], 1, 'utf8');
            $c2 = mb_substr($d['raw'], 0, 1, 'utf8');


            $this->assertSame($c1, $c2);
        }

        // raw field test
        // compare entire initial string with mounted through iteration
        $this->assertSame($data, $t);
    }
    protected function _var_dump($d) {
        ob_start();
        var_dump($d);
        $data = ob_get_clean();
        return mb_substr($data, 0, $this->_nthOccurrenceInString($data, "\n", 5) ?: 50, 'utf-8')."\n...\n";
    }
    protected function _nthOccurrenceInString($str, $char, $nth = 1, $encoding = 'utf-8') {

        $offset = -1;

        while ($nth) {
            $nth -= 1;
            if ( ($k = mb_strpos($str, $char, $offset + 1, $encoding)) > -1 ) {
                $offset = $k;
            }
            else {
                break;
            }
        }

        return $offset;
    }
}