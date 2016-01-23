<?php

namespace Stopsopa\Sax\Lib\Iterator;

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
    protected $blindlyPointer;

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

    protected $fetchedChunksNum;

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
    public function initialize($data, $encoding = 'utf8', $chunk = 1024) {

        if ($chunk < 20) {
            $chunk = 20;
        }

        $this->file = $data;

        $this->encoding = $encoding;

        $this->chunk = $chunk;


        $this->half = floor($chunk / 2);

        $this->double = $this->chunk * 2;

        if ($this->mode === static::MODE_FILE) { // file mode

            $this->handler = fopen($data, 'rb');
        }
        else { // string mode
            $this->handler = fopen('php://temp', 'r+');

            fwrite($this->handler, $data);
        }

        $this->reset();
    }
    public function __destruct()
    {
        fclose($this->handler);
    }
    public function reset()
    {
        rewind($this->handler);

        $this->tmp = $this->_chunk();

        $this->fetchedChunksNum = false;

        $this->blindlyPointer = 0;

        $this->pos = 0;

        return $this;
    }
    public function next() {

        if ($this->blindlyPointer > $this->half) {

            $this->blindlyPointer = 0;

            $tmp = $this->_chunk();

            if ($tmp) {

                $this->tmp .= $tmp;

                if (strlen($this->tmp) > $this->double ) {

                    $this->tmp = mb_substr($this->tmp, $this->pos, null, $this->encoding);

                    $this->pos = 0;

                    $this->blindlyPointer = 0;
                }
            }
        }

        $ret = mb_substr($this->tmp, $this->pos, 1, $this->encoding);

        $this->pos += 1;

        $this->blindlyPointer += 1;

        if (is_string($ret) && strlen($ret) > 0) {
            return $ret;
        }
    }
    protected function _chunk() {

        if (feof($this->handler)) {
            return null;
        }

        return fread($this->handler, $this->chunk);
    }
}