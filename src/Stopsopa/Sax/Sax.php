<?php

namespace Stopsopa\Sax;

use Iterator;
use Stopsopa\Sax\Exceptions\SaxException;
use Stopsopa\Sax\Lib\Iterator\StreamTextIterator;

class Sax implements Iterator
{
    const F_SPACES          = 1;
    const F_TAG             = 2;
    const F_DATA            = 3;

    const MODE_FILE         = 1;
    const MODE_STRING       = 2;

    protected $iterator;
    protected $event;
    protected $key;

    public function __construct($source, $event, $options = array())
    {
        $options = array_merge(array(
            'encoding' => 'utf8',
            'chunk' => null,
            'mode' => null
        ), $options);

        $this->event = $event;

        $this->iterator = new StreamTextIterator($options['mode']);
        $this->iterator->initialize($source, $options['encoding'], $options['chunk']);
    }
    /**
        rewind
        valid
        current
        next
        valid
        current
        next
        valid if return false, end
     */
    public function rewind()
    {
        $this->iterator->reset();
        $this->key = 0;
    }
    public function current()
    {
    }
    public function next()
    {
    }
    public function key()
    {
        return $this->key;
    }
    public function valid()
    {
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