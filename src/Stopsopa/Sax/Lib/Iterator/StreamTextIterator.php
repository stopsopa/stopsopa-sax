<?php

namespace Stopsopa\Sax\Lib\Iterator;
use Exception;

class StreamTextIterator {
    const MODE_FILE = 1;
    const MODE_STRING = 2;

    protected $mode;

    protected $encoding;
    protected $file;
    protected $handler;

    protected $tmp;

    /**
     * Jaki kawaek pliku ma odcinać za pomocą fread()
     * bytes length
     * @var int
     */
    protected $chunk;

    /**
     * bytes length
     * @var int
     */
    protected $checkpoint;

    /**
     * bytes length
     * @var float
     */
    protected $half;

    /**
     * bytes length
     * @var float
     */
    protected $double;

    /**
     * utf-8 length
     * @var int
     */
    protected $pos;

    protected $last;

    /**
     * StreamTextIterator constructor.
     * @param null $mode - default MODE_FILE
     */
    public function __construct($mode = null)
    {
        $this->mode = $mode;

        if (!in_array($this->mode, array(static::MODE_FILE, static::MODE_STRING))) {
            $this->mode = static::MODE_FILE;
        }
    }
    public function initialize($source, $encoding = 'utf8', $chunk = null) {

        if (is_null($chunk)) {
            $chunk = 1024;
        }

        if ($chunk < 20) {
            $chunk = 20;
        }

        $this->file = $source;

        $this->encoding = $encoding;

        $this->chunk = $chunk;


        $this->half = floor($chunk / 2);

        $this->double = $chunk * 2;

        if ($this->mode === static::MODE_FILE) { // file mode

            if (!file_exists($source)) {
                throw new Exception("File '$source' doesn't exists");
            }
            else {
                die('wtf?');
            }

            $this->handler = fopen($source, 'rb');
        }
        else { // string mode

            $this->handler = fopen('php://temp', 'r+');

            fwrite($this->handler, $source);
        }

        $this->reset();
    }
    public function __destruct()
    {
        if ($this->handler) {
            fclose($this->handler);
        }
    }
    public function reset()
    {
        rewind($this->handler);

        $this->tmp = $this->_chunk();

        $this->checkpoint = 0;

        $this->pos = 0;

        return $this;
    }
    public function next() {

        if ($this->checkpoint > $this->half) {

            $this->tmp = mb_substr($this->tmp, $this->pos, null, $this->encoding);

            $this->pos = $this->checkpoint = 0;

            if (strlen($this->tmp) < $this->double) {

                $tmp = $this->_chunk();

                if ($tmp) {
                    $this->tmp .= $tmp;
                }
            }
        }

        $ret = mb_substr($this->tmp, $this->pos, 1, $this->encoding);

        $this->pos += 1;

        if (is_string($ret)) {

            $len = strlen($ret);

            if ($len > 0) {

                $this->checkpoint += $len;

                return $this->last = $ret;
            }
        }

        return $this->last = null;
    }
    public function last() {
        return $this->last;
    }
    protected function _chunk() {

        if (feof($this->handler)) {
            return null;
        }

        return fread($this->handler, $this->chunk);
    }
//    protected function _d($d) {
//        ob_start();
//        var_dump($d);
//        $e = ob_get_clean();
//        $e = str_replace("\r\n", "\\r\\n", $e);
////        $e = str_replace("\r", "\\r", $e);
//        fwrite(STDOUT, $e."\n");
////        fwrite(STDERR, $e);
//    }
}