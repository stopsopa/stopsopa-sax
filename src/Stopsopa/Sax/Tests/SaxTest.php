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
[{"type":2,"raw":"<?xml version=\"1.0\" encoding=\"UTF-8\"?>","data":{"type":"opening","attr":{"version":"1.0","encoding":"UTF-8"},"name":"?xml"},"offset":0},{"type":1,"raw":"\\r\\n","offset":38},{"type":2,"raw":"<note>","data":{"type":"opening","attr":[],"name":"note"},"offset":40},{"type":5,"raw":"<!---->","data":"","offset":46},{"type":2,"raw":"<div>","data":{"type":"opening","attr":[],"name":"div"},"offset":53},{"type":1,"raw":" ","offset":58},{"type":5,"raw":"<!-- comment -->","data":" comment ","offset":59},{"type":1,"raw":"\\r\\n            ","offset":75},{"type":4,"raw":"<![CDATA[\\r\\nYou will see this in the document\\r\\nand can use reserved characters like\\r\\n< > & \"\\r\\n]]>","data":"\\r\\nYou will see this in the document\\r\\nand can use reserved characters like\\r\\n< > & \"\\r\\n","offset":89},{"type":1,"raw":"\\r\\n\\r\\n","offset":185},{"type":2,"raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":189},{"type":1,"raw":"\\r\\n        ","offset":195},{"type":2,"raw":"<span test=\"raz\" data-dwa=\"entity\" data-dwa=\"value2\" class=\"red blue\">","data":{"type":"opening","attr":{"test":"raz","data-dwa":["entity","value2"],"class":"red blue"},"name":"span"},"offset":205},{"type":3,"raw":"test span","offset":275},{"type":2,"raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":284},{"type":1,"raw":"\\r\\n        ","offset":291},{"type":2,"raw":"<div>","data":{"type":"opening","attr":[],"name":"div"},"offset":301},{"type":1,"raw":"\\r\\n\\r\\n            ","offset":306},{"type":4,"raw":"<![CDATA[\\r\\nYou will see this in the document\\r\\nand can use reserved characters <co\u015b tam co\u015b tam>like\\r\\n<b>bold<\/b>\\r\\n< > & \"\\r\\n]]>","data":"\\r\\nYou will see this in the document\\r\\nand can use reserved characters <co\u015b tam co\u015b tam>like\\r\\n<b>bold<\/b>\\r\\n< > & \"\\r\\n","offset":322},{"type":1,"raw":"\\r\\n        ","offset":448},{"type":2,"raw":"<bo a b c def=\"xyz\" j:i k:l='test' empty=\"\" one=\" 'a \"  two=' e\" ' spaces= \" spaceval \" key=value a b=\"ky\" ghi \u0105=\"war=to\u015b\u0107\" \u0119 test=\"\\r\\n        val\\r\\n        \" enter=\\r\\n        valenter\\r\\n        >","data":{"type":"opening","attr":{"def":"xyz","k:l":"test","empty":"","one":" 'a ","two":" e\" ","spaces":" spaceval ","b":["ky",null],"\u0105":"war=to\u015b\u0107","test":"\\r\\n        val\\r\\n        ","key":"value","enter":"valenter","a":[null,null],"c":null,"j:i":null,"ghi":null,"\u0119":null},"name":"bo"},"offset":458},{"type":3,"raw":"\\r\\n        ins>ide\\r\\n        ","offset":650},{"type":2,"raw":"<\/bo>","data":{"type":"closing","name":"bo"},"offset":677},{"type":1,"raw":"\\r\\n        ","offset":682},{"type":2,"raw":"<bo a b c def=\"xyz\" k:l='test' empty=\"\" key=value a b ghi \u0105=\"war=to\u015b\u0107\" \u0119 j:i>","data":{"type":"opening","attr":{"def":"xyz","k:l":"test","empty":"","\u0105":"war=to\u015b\u0107","key":"value","a":[null,null],"b":[null,null],"c":null,"ghi":null,"\u0119":null,"j:i":null},"name":"bo"},"offset":692},{"type":2,"raw":"<\/bo>","data":{"type":"closing","name":"bo"},"offset":769},{"type":1,"raw":"\\r\\n        ","offset":774},{"type":2,"raw":"<bo a b c def=\"xyz\" j:i k:l='test' empty=\"\" key=value a b ghi \u0105=\"war=to\u015b\u0107\"\/>","data":{"type":"empty","attr":{"def":"xyz","k:l":"test","empty":"","\u0105":"war=to\u015b\u0107","key":"value","a":[null,null],"b":[null,null],"c":null,"j:i":null,"ghi":null},"name":"bo"},"offset":784},{"type":1,"raw":"\\r\\n        ","offset":860},{"type":2,"raw":"<bo a b c def=\"xyz\" k:l='test' empty=\"\" key=value a j:i b ghi \u0105=\"war=to\u015b\u0107\" \u0119 \/>","data":{"type":"empty","attr":{"def":"xyz","k:l":"test","empty":"","\u0105":"war=to\u015b\u0107","key":"value","a":[null,null],"b":[null,null],"c":null,"j:i":null,"ghi":null,"\u0119":null},"name":"bo"},"offset":870},{"type":1,"raw":"\\r\\n        ","offset":949},{"type":2,"raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":959},{"type":1,"raw":" ","offset":965},{"type":5,"raw":"<!-- comment2 -->","data":" comment2 ","offset":966},{"type":1,"raw":"\\r\\n        ","offset":983},{"type":2,"raw":"<div:test>","data":{"type":"opening","attr":[],"name":"div:test"},"offset":993},{"type":1,"raw":"\\r\\n            ","offset":1003},{"type":4,"raw":"<![CDATA[]]>","data":"","offset":1017},{"type":1,"raw":"\\r\\n        ","offset":1029},{"type":2,"raw":"<\/div:test>","data":{"type":"closing","name":"div:test"},"offset":1039},{"type":1,"raw":"\\r\\n        ","offset":1050},{"type":2,"raw":"<span \/>","data":{"type":"empty","attr":[],"name":"span"},"offset":1060},{"type":1,"raw":"\\r\\n        ","offset":1068},{"type":2,"raw":"<span>","data":{"type":"opening","attr":[],"name":"span"},"offset":1078},{"type":3,"raw":"test","offset":1084},{"type":2,"raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":1088},{"type":1,"raw":"\\r\\n        ","offset":1095},{"type":2,"raw":"<span:split te:st=\"value'valuepart2\" te:st2\/>","data":{"type":"empty","attr":{"te:st":"value'valuepart2","te:st2":null},"name":"span:split"},"offset":1105},{"type":1,"raw":"\\r\\n        ","offset":1150},{"type":2,"raw":"<span:split te:st=\"value'valuepart2\" te:st2 \/>","data":{"type":"empty","attr":{"te:st":"value'valuepart2","te:st2":null},"name":"span:split"},"offset":1160},{"type":1,"raw":"\\r\\n        ","offset":1206},{"type":2,"raw":"<span:split2>","data":{"type":"opening","attr":[],"name":"span:split2"},"offset":1216},{"type":2,"raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":1229},{"type":1,"raw":"\\r\\n        ","offset":1242},{"type":2,"raw":"<span:split2>","data":{"type":"opening","attr":[],"name":"span:split2"},"offset":1252},{"type":3,"raw":"some\\r\\n        data","offset":1265},{"type":2,"raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":1283},{"type":1,"raw":"\\r\\n        ","offset":1296},{"type":2,"raw":"<span:split2>","data":{"type":"opening","attr":[],"name":"span:split2"},"offset":1306},{"type":4,"raw":"<![CDATA[<span>cdata<\/span>]]>","data":"<span>cdata<\/span>","offset":1319},{"type":2,"raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":1349},{"type":1,"raw":"\\r\\n        ","offset":1362},{"type":2,"raw":"<span:split2>","data":{"type":"opening","attr":[],"name":"span:split2"},"offset":1372},{"type":1,"raw":"\\r\\n        ","offset":1385},{"type":4,"raw":"<![CDATA[\\r\\n        <span>cdata<\/span>\\r\\n        ]]>","data":"\\r\\n        <span>cdata<\/span>\\r\\n        ","offset":1395},{"type":1,"raw":"\\r\\n        ","offset":1445},{"type":2,"raw":"<\/span:split>","data":{"type":"closing","name":"span:split"},"offset":1455},{"type":1,"raw":"\\r\\n        ","offset":1468},{"type":2,"raw":"<div>","data":{"type":"opening","attr":[],"name":"div"},"offset":1478},{"type":1,"raw":"\\r\\n            ","offset":1483},{"type":4,"raw":"<![CDATA[test ]>]]>","data":"test ]>","offset":1497},{"type":1,"raw":"\\r\\n\\r\\n        ","offset":1516},{"type":2,"raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":1528},{"type":1,"raw":"\\r\\n        ","offset":1534},{"type":2,"raw":"<span>","data":{"type":"opening","attr":[],"name":"span"},"offset":1544},{"type":3,"raw":"span data","offset":1550},{"type":2,"raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":1559},{"type":1,"raw":"\\r\\n        ","offset":1566},{"type":2,"raw":"<div>","data":{"type":"opening","attr":[],"name":"div"},"offset":1576},{"type":1,"raw":"\\r\\n            ","offset":1581},{"type":4,"raw":"<![CDATA[<span>test<\/span>]]>","data":"<span>test<\/span>","offset":1595},{"type":1,"raw":"\\r\\n\\r\\n        ","offset":1624},{"type":2,"raw":"<\/div>","data":{"type":"closing","name":"div"},"offset":1636},{"type":1,"raw":"\\r\\n        ","offset":1642},{"type":2,"raw":"<span test=\"raz\" data-dwa=\"entity\" data-dwa=\"value2\" class=\"red blue\">","data":{"type":"opening","attr":{"test":"raz","data-dwa":["entity","value2"],"class":"red blue"},"name":"span"},"offset":1652},{"type":3,"raw":"test span","offset":1722},{"type":2,"raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":1731},{"type":1,"raw":"\\r\\n        ","offset":1738},{"type":2,"raw":"<span src=\"image\">","data":{"type":"opening","attr":{"src":"image"},"name":"span"},"offset":1748},{"type":1,"raw":"\\r\\n        ","offset":1766},{"type":2,"raw":"<span test=\"raz\" data-dwa=\"entity\" data-dwa=\"value2\" class=\"red blue\"\/>","data":{"type":"empty","attr":{"test":"raz","data-dwa":["entity","value2"],"class":"red blue"},"name":"span"},"offset":1776},{"type":3,"raw":"test span","offset":1847},{"type":2,"raw":"<\/span>","data":{"type":"closing","name":"span"},"offset":1856},{"type":1,"raw":"\\r\\n    ","offset":1863},{"type":2,"raw":"<\/test>","data":{"type":"closing","name":"test"},"offset":1869},{"type":1,"raw":"\\r\\n","offset":1876},{"type":2,"raw":"<\/note>","data":{"type":"closing","name":"note"},"offset":1878}]
eos;

        $check = json_decode($check, true);

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
        <bo a b c def="xyz" j:i k:l='test' empty="" one=" 'a "  two=' e" ' spaces= " spaceval " key=value a b="ky" ghi ą="war=tość" ę test="
        val
        " enter=
        valenter
        >
        ins>ide
        </bo>
        <bo a b c def="xyz" k:l='test' empty="" key=value a b ghi ą="war=tość" ę j:i></bo>
        <bo a b c def="xyz" j:i k:l='test' empty="" key=value a b ghi ą="war=tość"/>
        <bo a b c def="xyz" k:l='test' empty="" key=value a j:i b ghi ą="war=tość" ę />
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
}