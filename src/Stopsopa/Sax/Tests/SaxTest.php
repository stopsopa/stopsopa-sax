<?php

namespace Stopsopa\Sax\Tests;

use Stopsopa\Sax\Sax;

class SaxTest extends \PHPUnit_Framework_TestCase {

    public function testSimpleXml() {
        $data = <<<eos
      <?xml version="1.0" encoding="UTF-8"?>
<note>
    <span>
    data
    </span>
	<to>Tove</to>
	<from>Jani</from>
	<heading>Remiąćęńółnder</heading>
	<bo ą ąśżźćęół dy>Don't
	forget me this weekend!</body>
	<!-- comment -->
</note>
<!---->
<koniec>
<!-- jfdsk <!-- -- -->
eos;
        $types = json_decode('[1,2,1,2,1,2,3,2,1,2,3,2,1,2,3,2,1,2,3,2,1,2,3,2,1,5,1,2,1,5,1,2,1,5,1]', true);

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
[{"type":2,"raw":"<?xml version=\"1.0\" encoding=\"UTF-8\"?>","data":{"type":"opening","name":"?xml","attr":{"versio":"1.0","encoding":"UTF-8"}},"offset":0},{"type":1,"raw":"\\r\\n","offset":38},{"type":2,"raw":"<note>","data":{"type":"opening","name":"note","attr":[]},"offset":40},{"type":5,"raw":"<!---->","data":"","offset":46},{"type":2,"raw":"<div>","data":{"type":"opening","name":"div","attr":[]},"offset":53},{"type":1,"raw":" ","offset":58},{"type":5,"raw":"<!-- comment -->","data":" comment ","offset":59},{"type":1,"raw":"\\r\\n            ","offset":75},{"type":4,"raw":"<![CDATA[\\r\\nYou will see this in the document\\r\\nand can use reserved characters like\\r\\n< > & \"\\r\\n]]>","data":"\\r\\nYou will see this in the document\\r\\nand can use reserved characters like\\r\\n< > & \"\\r\\n","offset":89},{"type":1,"raw":"\\r\\n\\r\\n","offset":185},{"type":2,"raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":189},{"type":1,"raw":"\\r\\n        ","offset":195},{"type":2,"raw":"<span test=\"raz\" data-dwa=\"entity\" data-dwa=\"value2\" class=\"red blue\">","data":{"type":"opening","name":"span","attr":{"es":"raz","data-dwa":["entity","value2"],"class":"red","blue\"":null}},"offset":205},{"type":3,"raw":"test span","offset":275},{"type":2,"raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":284},{"type":1,"raw":"\\r\\n        ","offset":291},{"type":2,"raw":"<div>","data":{"type":"opening","name":"div","attr":[]},"offset":301},{"type":1,"raw":"\\r\\n\\r\\n            ","offset":306},{"type":4,"raw":"<![CDATA[\\r\\nYou will see this in the document\\r\\nand can use reserved characters <co\u015b tam co\u015b tam>like\\r\\n<b>bold<\/b>\\r\\n< > & \"\\r\\n]]>","data":"\\r\\nYou will see this in the document\\r\\nand can use reserved characters <co\u015b tam co\u015b tam>like\\r\\n<b>bold<\/b>\\r\\n< > & \"\\r\\n","offset":322},{"type":1,"raw":"\\r\\n        ","offset":448},{"type":2,"raw":"<bo a b c def=\"xyz\" j:i k:l='test' empty=\"\" key=value a b ghi jkl \u0105=\"war=to\u015b\u0107\" \u0119>","data":{"type":"opening","name":"bo","attr":{"a":[null,null],"b":[null,null],"c":null,"def":"xyz","j:i":null,"k:l":"test","empty":"","key":"value","ghi":null,"jkl":null,"\u0105":"war=to\u015b\u0107","\u0119":null}},"offset":458},{"type":1,"raw":"\\r\\n        ","offset":539},{"type":3,"raw":"inside\\r\\n        ","offset":549},{"type":2,"raw":"<\/bo>","data":{"type":"closing","name":"bo"},"offset":565},{"type":1,"raw":"\\r\\n        ","offset":570},{"type":2,"raw":"<bo a b c def=\"xyz\" j:i k:l='test' empty=\"\" key=value a b ghi jkl \u0105=\"war=to\u015b\u0107\" \u0119>","data":{"type":"opening","name":"bo","attr":{"a":[null,null],"b":[null,null],"c":null,"def":"xyz","j:i":null,"k:l":"test","empty":"","key":"value","ghi":null,"jkl":null,"\u0105":"war=to\u015b\u0107","\u0119":null}},"offset":580},{"type":2,"raw":"<\/bo>","data":{"type":"closing","name":"bo"},"offset":661},{"type":1,"raw":"\\r\\n        ","offset":666},{"type":2,"raw":"<bo a b c def=\"xyz\" j:i k:l='test' empty=\"\" key=value a b ghi jkl \u0105=\"war=to\u015b\u0107\"\/>","data":{"type":"empty","name":"bo","attr":{"a":[null,null],"b":[null,null],"c":null,"def":"xyz","j:i":null,"k:l":"test","empty":"","key":"value","ghi":null,"jkl":null,"\u0105":"war=to\u015b\u0107"}},"offset":676},{"type":1,"raw":"\\r\\n        ","offset":756},{"type":2,"raw":"<bo a b c def=\"xyz\" j:i k:l='test' empty=\"\" key=value a b ghi jkl \u0105=\"war=to\u015b\u0107\" \u0119 \/>","data":{"type":"empty","name":"bo","attr":{"a":[null,null],"b":[null,null],"c":null,"def":"xyz","j:i":null,"k:l":"test","empty":"","key":"value","ghi":null,"jkl":null,"\u0105":"war=to\u015b\u0107","\u0119":null}},"offset":766},{"type":1,"raw":"\\r\\n        ","offset":849},{"type":2,"raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":859},{"type":1,"raw":" ","offset":865},{"type":5,"raw":"<!-- comment2 -->","data":" comment2 ","offset":866},{"type":1,"raw":"\\r\\n        ","offset":883},{"type":2,"raw":"<div:test>","data":{"type":"opening","name":"div:test","attr":[]},"offset":893},{"type":1,"raw":"\\r\\n            ","offset":903},{"type":4,"raw":"<![CDATA[]]>","data":"","offset":917},{"type":1,"raw":"\\r\\n        ","offset":929},{"type":2,"raw":"<\/div:test>","data":{"type":"closing","name":"div:test"},"offset":939},{"type":1,"raw":"\\r\\n        ","offset":950},{"type":2,"raw":"<span \/>","data":{"type":"empty","name":"span","attr":[]},"offset":960},{"type":1,"raw":"\\r\\n        ","offset":968},{"type":2,"raw":"<span>","data":{"type":"opening","name":"span","attr":[]},"offset":978},{"type":3,"raw":"test","offset":984},{"type":2,"raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":988},{"type":1,"raw":"\\r\\n        ","offset":995},{"type":2,"raw":"<span:split te:st=\"value'valuepart2\" te:st2\/>","data":{"type":"empty","name":"span:split","attr":{"e:s":"value'valuepart2","e:st2":null}},"offset":1005},{"type":1,"raw":"\\r\\n        ","offset":1050},{"type":2,"raw":"<span:split te:st=\"value'valuepart2\" te:st2 \/>","data":{"type":"empty","name":"span:split","attr":{"e:s":"value'valuepart2","e:st2":null}},"offset":1060},{"type":1,"raw":"\\r\\n        ","offset":1106},{"type":2,"raw":"<span:split2>","data":{"type":"opening","name":"span:split2","attr":[]},"offset":1116},{"type":2,"raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":1129},{"type":1,"raw":"\\r\\n        ","offset":1142},{"type":2,"raw":"<span:split2>","data":{"type":"opening","name":"span:split2","attr":[]},"offset":1152},{"type":3,"raw":"some\\r\\n        data","offset":1165},{"type":2,"raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":1183},{"type":1,"raw":"\\r\\n        ","offset":1196},{"type":2,"raw":"<span:split2>","data":{"type":"opening","name":"span:split2","attr":[]},"offset":1206},{"type":4,"raw":"<![CDATA[<span>cdata<\/span>]]>","data":"<span>cdata<\/span>","offset":1219},{"type":2,"raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":1249},{"type":1,"raw":"\\r\\n        ","offset":1262},{"type":2,"raw":"<span:split2>","data":{"type":"opening","name":"span:split2","attr":[]},"offset":1272},{"type":1,"raw":"\\r\\n        ","offset":1285},{"type":4,"raw":"<![CDATA[\\r\\n        <span>cdata<\/span>\\r\\n        ]]>","data":"\\r\\n        <span>cdata<\/span>\\r\\n        ","offset":1295},{"type":1,"raw":"\\r\\n        ","offset":1345},{"type":2,"raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":1355},{"type":1,"raw":"\\r\\n        ","offset":1368},{"type":2,"raw":"<div>","data":{"type":"opening","name":"div","attr":[]},"offset":1378},{"type":1,"raw":"\\r\\n            ","offset":1383},{"type":4,"raw":"<![CDATA[test ]>]]>","data":"test ]>","offset":1397},{"type":1,"raw":"\\r\\n\\r\\n        ","offset":1416},{"type":2,"raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":1428},{"type":1,"raw":"\\r\\n        ","offset":1434},{"type":2,"raw":"<span>","data":{"type":"opening","name":"span","attr":[]},"offset":1444},{"type":3,"raw":"span data","offset":1450},{"type":2,"raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":1459},{"type":1,"raw":"\\r\\n        ","offset":1466},{"type":2,"raw":"<div>","data":{"type":"opening","name":"div","attr":[]},"offset":1476},{"type":1,"raw":"\\r\\n            ","offset":1481},{"type":4,"raw":"<![CDATA[<span>test<\/span>]]>","data":"<span>test<\/span>","offset":1495},{"type":1,"raw":"\\r\\n\\r\\n        ","offset":1524},{"type":2,"raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":1536},{"type":1,"raw":"\\r\\n        ","offset":1542},{"type":2,"raw":"<span test=\"raz\" data-dwa=\"entity\" data-dwa=\"value2\" class=\"red blue\">","data":{"type":"opening","name":"span","attr":{"es":"raz","data-dwa":["entity","value2"],"class":"red","blue\"":null}},"offset":1552},{"type":3,"raw":"test span","offset":1622},{"type":2,"raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":1631},{"type":1,"raw":"\\r\\n        ","offset":1638},{"type":2,"raw":"<span src=\"image\">","data":{"type":"opening","name":"span","attr":{"src":"image"}},"offset":1648},{"type":1,"raw":"\\r\\n        ","offset":1666},{"type":2,"raw":"<span test=\"raz\" data-dwa=\"entity\" data-dwa=\"value2\" class=\"red blue\"\/>","data":{"type":"empty","name":"span","attr":{"es":"raz","data-dwa":["entity","value2"],"class":"red","blue\"":null}},"offset":1676},{"type":3,"raw":"test span","offset":1747},{"type":2,"raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":1756},{"type":1,"raw":"\\r\\n    ","offset":1763},{"type":2,"raw":"<\/test>","data":{"type":"closing","name":"test"},"offset":1769},{"type":1,"raw":"\\r\\n","offset":1776},{"type":2,"raw":"<\/note>","data":{"type":"closing","name":"note"},"offset":1778}]
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
        <bo a b c def="xyz" j:i k:l='test' empty="" key=value a b ghi jkl ą="war=tość" ę>
        ins>ide
        </bo>
        <bo a b c def="xyz" j:i k:l='test' empty="" key=value a b ghi jkl ą="war=tość" ę></bo>
        <bo a b c def="xyz" j:i k:l='test' empty="" key=value a b ghi jkl ą="war=tość"/>
        <bo a b c def="xyz" j:i k:l='test' empty="" key=value a b ghi jkl ą="war=tość" ę />
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