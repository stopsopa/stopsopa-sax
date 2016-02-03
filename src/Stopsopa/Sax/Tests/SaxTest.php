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


//      change rn for travis-ci
//      die(str_replace("\r\n", "\\r\\n", json_encode($s)));

        $check = <<<eos
[{"type":"t","raw":"<?xml version=\"1.0\" encoding=\"UTF-8\"?>","data":{"type":"opening","name":"?xml","attr":{"version":"1.0","encoding":"UTF-8"}},"offset":0},{"type":"s","raw":"\\r\\n","offset":38},{"type":"t","raw":"<note>","data":{"type":"opening","name":"note","attr":[]},"offset":40},{"type":"co","raw":"<!---->","data":"","offset":46},{"type":"t","raw":"<div>","data":{"type":"opening","name":"div","attr":[]},"offset":53},{"type":"s","raw":" ","offset":58},{"type":"co","raw":"<!-- comment -->","data":" comment ","offset":59},{"type":"s","raw":"\\r\\n            ","offset":75},{"type":"cd","raw":"<![CDATA[\\r\\nYou will see this in the document\\r\\nand can use reserved characters like\\r\\n< > & \"\\r\\n]]>","data":"\\r\\nYou will see this in the document\\r\\nand can use reserved characters like\\r\\n< > & \"\\r\\n","offset":89},{"type":"s","raw":"\\r\\n\\r\\n","offset":185},{"type":"t","raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":189},{"type":"s","raw":"\\r\\n        ","offset":195},{"type":"t","raw":"<span test=\"raz\" data-dwa=\"entity\" data-dwa=\"value2\" class=\"red blue\">","data":{"type":"opening","name":"span","attr":{"test":"raz","data-dwa":["entity","value2"],"class":"red blue"}},"offset":205},{"type":"d","raw":"test span","offset":275},{"type":"t","raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":284},{"type":"s","raw":"\\r\\n        ","offset":291},{"type":"t","raw":"<div>","data":{"type":"opening","name":"div","attr":[]},"offset":301},{"type":"s","raw":"\\r\\n\\r\\n            ","offset":306},{"type":"cd","raw":"<![CDATA[\\r\\nYou will see this in the document\\r\\nand can use reserved characters <co\u015b tam co\u015b tam>like\\r\\n<b>bold<\/b>\\r\\n< > & \"\\r\\n]]>","data":"\\r\\nYou will see this in the document\\r\\nand can use reserved characters <co\u015b tam co\u015b tam>like\\r\\n<b>bold<\/b>\\r\\n< > & \"\\r\\n","offset":322},{"type":"s","raw":"\\r\\n        ","offset":448},{"type":"t","raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":458},{"type":"s","raw":" ","offset":464},{"type":"co","raw":"<!-- comment2 -->","data":" comment2 ","offset":465},{"type":"s","raw":"\\r\\n        ","offset":482},{"type":"t","raw":"<div:test>","data":{"type":"opening","name":"div:test","attr":[]},"offset":492},{"type":"s","raw":"\\r\\n            ","offset":502},{"type":"cd","raw":"<![CDATA[]]>","data":"","offset":516},{"type":"s","raw":"\\r\\n        ","offset":528},{"type":"t","raw":"<\/div:test>","data":{"type":"closing","name":"div:test"},"offset":538},{"type":"s","raw":"\\r\\n        ","offset":549},{"type":"t","raw":"<span \/>","data":{"type":"empty","name":"span","attr":[]},"offset":559},{"type":"s","raw":"\\r\\n        ","offset":567},{"type":"t","raw":"<span>","data":{"type":"opening","name":"span","attr":[]},"offset":577},{"type":"d","raw":"test","offset":583},{"type":"t","raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":587},{"type":"s","raw":"\\r\\n        ","offset":594},{"type":"t","raw":"<span:split te:st=\"value'valuepart2\" te:st2\/>","data":{"type":"empty","name":"span:split","attr":{"te:st":"value'valuepart2","te:st2":null}},"offset":604},{"type":"s","raw":"\\r\\n        ","offset":649},{"type":"t","raw":"<span:split te:st=\"value'valuepart2\" te:st2 \/>","data":{"type":"empty","name":"span:split","attr":{"te:st":"value'valuepart2","te:st2":null}},"offset":659},{"type":"s","raw":"\\r\\n        ","offset":705},{"type":"t","raw":"<span:split2>","data":{"type":"opening","name":"span:split2","attr":[]},"offset":715},{"type":"t","raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":728},{"type":"s","raw":"\\r\\n        ","offset":741},{"type":"t","raw":"<span:split2>","data":{"type":"opening","name":"span:split2","attr":[]},"offset":751},{"type":"d","raw":"some\\r\\n        data","offset":764},{"type":"t","raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":782},{"type":"s","raw":"\\r\\n        ","offset":795},{"type":"t","raw":"<span:split2>","data":{"type":"opening","name":"span:split2","attr":[]},"offset":805},{"type":"cd","raw":"<![CDATA[<span>cdata<\/span>]]>","data":"<span>cdata<\/span>","offset":818},{"type":"t","raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":848},{"type":"s","raw":"\\r\\n        ","offset":861},{"type":"t","raw":"<span:split2>","data":{"type":"opening","name":"span:split2","attr":[]},"offset":871},{"type":"s","raw":"\\r\\n        ","offset":884},{"type":"cd","raw":"<![CDATA[\\r\\n        <span>cdata<\/span>\\r\\n        ]]>","data":"\\r\\n        <span>cdata<\/span>\\r\\n        ","offset":894},{"type":"s","raw":"\\r\\n        ","offset":944},{"type":"t","raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":954},{"type":"s","raw":"\\r\\n        ","offset":967},{"type":"t","raw":"<div>","data":{"type":"opening","name":"div","attr":[]},"offset":977},{"type":"s","raw":"\\r\\n            ","offset":982},{"type":"cd","raw":"<![CDATA[test ]>]]>","data":"test ]>","offset":996},{"type":"s","raw":"\\r\\n\\r\\n        ","offset":1015},{"type":"t","raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":1027},{"type":"s","raw":"\\r\\n        ","offset":1033},{"type":"t","raw":"<span>","data":{"type":"opening","name":"span","attr":[]},"offset":1043},{"type":"d","raw":"span data","offset":1049},{"type":"t","raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":1058},{"type":"s","raw":"\\r\\n        ","offset":1065},{"type":"t","raw":"<div>","data":{"type":"opening","name":"div","attr":[]},"offset":1075},{"type":"s","raw":"\\r\\n            ","offset":1080},{"type":"cd","raw":"<![CDATA[<span>test<\/span>]]>","data":"<span>test<\/span>","offset":1094},{"type":"s","raw":"\\r\\n\\r\\n        ","offset":1123},{"type":"t","raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":1135},{"type":"s","raw":"\\r\\n        ","offset":1141},{"type":"t","raw":"<span test=\"raz\" data-dwa=\"entity\" data-dwa=\"value2\" class=\"red blue\">","data":{"type":"opening","name":"span","attr":{"test":"raz","data-dwa":["entity","value2"],"class":"red blue"}},"offset":1151},{"type":"d","raw":"test span","offset":1221},{"type":"t","raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":1230},{"type":"s","raw":"\\r\\n        ","offset":1237},{"type":"t","raw":"<span src=\"image\">","data":{"type":"opening","name":"span","attr":{"src":"image"}},"offset":1247},{"type":"s","raw":"\\r\\n        ","offset":1265},{"type":"t","raw":"<span test=\"raz\" data-dwa=\"entity\" data-dwa=\"value2\" class=\"red blue\"\/>","data":{"type":"empty","name":"span","attr":{"test":"raz","data-dwa":["entity","value2"],"class":"red blue"}},"offset":1275},{"type":"d","raw":"test span","offset":1346},{"type":"t","raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":1355},{"type":"s","raw":"\\r\\n    ","offset":1362},{"type":"t","raw":"<\/test>","data":{"type":"closing","name":"test"},"offset":1368},{"type":"s","raw":"\\r\\n","offset":1375},{"type":"t","raw":"<\/note>","data":{"type":"closing","name":"note"},"offset":1377}]
eos;

        $check = json_decode($check, true);

//        if (!$check) {
//            $check = str_replace("\r\n", "\\r\\n", json_decode($check, true));
//        }

//        if (!$check) {
//            $this->assertNotSame($check, null);
//        }

        $data = <<<eos
<?xml version="1.0" encoding="UTF-8"?>
<note><!----><div> <!-- comment -->
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
        </div> <!-- comment2 -->
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
//    protected function _var_dump($d) {
//        ob_start();
//        var_dump($d);
//        $data = ob_get_clean();
//        return mb_substr($data, 0, $this->_nthOccurrenceInString($data, "\n", 15) ?: 50, 'utf-8')."\n...\n";
//    }
//    protected function _nthOccurrenceInString($str, $char, $nth = 1, $encoding = 'utf-8') {
//
//        $offset = -1;
//
//        while ($nth) {
//            $nth -= 1;
//            if ( ($k = mb_strpos($str, $char, $offset + 1, $encoding)) > -1 ) {
//                $offset = $k;
//            }
//            else {
//                break;
//            }
//        }
//
//        return $offset;
//    }
//    test packagist hook
}