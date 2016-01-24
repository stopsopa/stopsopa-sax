<?php

namespace Stopsopa\Sax\Tests\Lib\Iterator;

use PHPUnit_Framework_TestCase;
use Stopsopa\Sax\Lib\Iterator\StreamTextIterator;

class StreamTextIteratorTest extends PHPUnit_Framework_TestCase {

    protected $range = array(20, 21, 30, 32, 34, 45, 36, 37, 39, 40, 41, 1024, 2048);
//    protected $range = array(20);
    public function testIterator() {

        $list = array(
            __DIR__.'/files/small_file.txt',
            __DIR__.'/files/only_ascii.txt',
            __DIR__.'/files/various_chars.txt',
            __DIR__.'/files/only_china_like_unicode.txt'
        );

        foreach ($list as $f) {

            $this->_open($f);

            $this->_testFile($f);
            $this->_testString($f);
        }
    }
    protected function _testFile($file) {

        $iter = new StreamTextIterator(StreamTextIterator::MODE_FILE);

        foreach ($this->range as $chunk) {

            $this->_d('file   : '.$chunk.' : '.$file);

            $iter->initialize($file, 'utf-8', $chunk);

            $i = 0;

            while (is_string($tmp = $iter->next())) {

                $this->assertSame($tmp, $this->_next($i));

                $i += 1;
            }

            $this->assertSame($iter->next(), $this->_next($i));
        }
    }
    protected function _testString($file) {

        $iter = new StreamTextIterator(StreamTextIterator::MODE_STRING);

        foreach ($this->range as $chunk) {

            $this->_d('string : '.$chunk.' : '.$file);

            $iter->initialize(file_get_contents($file), 'utf-8', $chunk);

            $i = 0;

            while (is_string($tmp = $iter->next())) {

                $this->assertSame($tmp, $this->_next($i));

                $i += 1;
            }

            $this->assertSame($iter->next(), $this->_next($i));
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
    protected function _next($i) {
        if ($i < $this->l) {
            return mb_substr($this->tmp, $i, 1, $this->encoding);
        }
    }
    protected function _d($d) {
        ob_start();
        var_dump($d);
        $e = ob_get_clean();
        fwrite(STDOUT, $e);
//        fwrite(STDERR, $e);
    }
}