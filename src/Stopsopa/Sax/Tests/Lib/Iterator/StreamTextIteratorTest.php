<?php

namespace Stopsopa\Sax\Tests\Lib\Iterator;

use PHPUnit_Framework_TestCase;
use Stopsopa\Sax\Lib\Iterator\StreamTextIterator;

class StreamTextIteratorTest extends PHPUnit_Framework_TestCase {

    public function testIterator() {
        foreach (array(
            __DIR__.'/short.txt',
            __DIR__.'/testdata.txt'
        ) as $f) {
            $this->_testFile($f);
            $this->_testString($f);
        }
    }
    protected function _testFile($file) {

        $this->_open($file);

        $iter = new StreamTextIterator(StreamTextIterator::MODE_FILE);

        foreach (array(20, 21, 30, 31, 32, 33, 34, 45, 36, 37, 38, 39, 40, 41, 500, 1024, 2048) as $chunk) {

            $iter->initialize($file, 'utf-8', $chunk);

            $i = 0;

            while (is_string($tmp = $iter->next())) {

                $this->assertSame($tmp, $this->_next($i));

                $i += 1;
            }
        }
    }
    protected function _testString($file) {

        $this->_open($file);

        $iter = new StreamTextIterator(StreamTextIterator::MODE_STRING);

        foreach (array(20, 21, 30, 31, 32, 33, 34, 45, 36, 37, 38, 39, 40, 41, 500, 1024, 2048) as $chunk) {

            $iter->initialize(file_get_contents($file), 'utf-8', $chunk);

            $i = 0;

            while (is_string($tmp = $iter->next())) {

                $this->assertSame($tmp, $this->_next($i));

                $i += 1;
            }
        }
    }
    protected function _next($i) {
        if ($i < $this->l) {
            return mb_substr($this->tmp, $i, 1, $this->encoding);
        }
    }
    protected $tmp;
    protected $l;
    protected $encoding;
    protected function _open($file, $encoding = 'utf-8') {
        $this->tmp = file_get_contents($file);
        $this->l = mb_strlen($this->tmp, $encoding);
        $this->encoding = $encoding;
    }
    protected function _d($d) {
        ob_start();
        var_dump($d);
        $e = ob_get_clean();
        fwrite(STDOUT, $e);
//        fwrite(STDERR, $e);
    }
}