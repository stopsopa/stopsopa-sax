<?php

namespace Stopsopa\Sax;

use Iterator;
use Stopsopa\Sax\Exceptions\SaxException;
use Stopsopa\Sax\Lib\Iterator\StreamTextIterator;

class Sax implements Iterator
{
    const F_SPACES = 's';
    const F_TAG = 't';
    const F_DATA = 'd';
    const F_CDATA = 'c';

    const MODE_FILE = 1;
    const MODE_STRING = 2;

    protected $iterator;
    protected $event;
    protected $key;

    protected $detectedState;

    protected $cache;
    protected $cchar;

    protected $c;

    protected $cdata_end_1;
    protected $cdata_end_2;
    protected $cdata_end_3;

    public function __construct($source, $options = array())
    {
        $options = array_merge(array(
            'encoding' => 'utf8',
            'chunk' => null,
            'mode' => null
        ), $options);

        $this->iterator = new StreamTextIterator($options['mode']);
        $this->iterator->initialize($source, $options['encoding'], $options['chunk']);
        $this->rewind();
    }
    protected function _setChar($c) {
        $this->cchar = $c;
        return $this;
    }
    protected function _popChar() {
        $k = $this->cchar;
        $this->cchar = false;
        return $k;
    }
    protected function _getChar() {
        return $this->cchar;
    }

    /**
     * rewind
     * valid
     * current
     * next
     * valid
     * current
     * next
     * valid if return false, end
     */
    public function rewind()
    {
        $this->iterator->reset();

        $this->key = 0;

        $this->_popChar();

        $this->detectedState = self::F_SPACES;

        $this->next();
    }

    public function current()
    {
        if ($this->cache) {
            return $this->cache;
        }
    }

    public function next()
    {
        $this->c = 0;

        $this->cache = '';

        $this->cdata_end_1 = $this->cdata_end_2 = $this->cdata_end_3 = null;

        $this->detectedState = null;

        if (is_string($this->_getChar())) {

            $t = $this->_popChar();

            if ($this->_cycle($t)) {

                return;
            }
        }

        while (is_string($t = $this->iterator->next())) {

            $this->key += 1;

            if ($this->_cycle($t)) {

                return;
            }
        }

        if (is_string($this->cache) && strlen($this->cache) > 0) {
            $this->cache = array(
                'type' => $this->detectedState,
                'data' => $this->cache
            );
        }
        else {
            $this->cache = null;

            $this->c = null;

            $this->key = 0;
        }
    }

    /**
     * @param $t
     * @return false - continue, true - break
     */
    protected function _cycle(&$t)
    {
        if ($this->c === 0) {

            if ($t === '<') {
                $this->c += 1;
                $this->cache .= $t;

                $this->detectedState = static::F_TAG;

                return false;
            }

            if ($this->_isWhiteChar($t)) {
                $this->c += 1;
                $this->cache .= $t;

                $this->detectedState = static::F_SPACES;

                return false;
            }

            $this->c += 1;
            $this->cache .= $t;

            $this->detectedState = static::F_DATA;

            return false;
        }

        if ($this->detectedState === static::F_DATA) {
            if ($t === '<') {

                $this->_setChar($t);

                $this->cache = array(
                    'type' => static::F_DATA,
                    'data' => $this->cache
                );

                return true;
            }

            $this->c += 1;
            $this->cache .= $t;

            $this->detectedState = static::F_DATA;

            return false;
        }

        if ($this->detectedState === static::F_TAG) {
            if ($this->c === 1 && $t === '!') {
                $this->c += 1;
                $this->cache .= $t;

                return false;
            }

            if ($this->c === 2 && $t === '[') {
                $this->c += 1;
                $this->cache .= $t;

                return false;
            }

            if ($this->c === 3 && strtolower($t) === 'c') {
                $this->c += 1;
                $this->cache .= $t;

                return false;
            }

            if ($this->c === 4 && strtolower($t) === 'd') {
                $this->c += 1;
                $this->cache .= $t;

                return false;
            }

            if ($this->c === 5 && strtolower($t) === 'a') {
                $this->c += 1;
                $this->cache .= $t;

                return false;
            }

            if ($this->c === 6 && strtolower($t) === 't') {
                $this->c += 1;
                $this->cache .= $t;

                return false;
            }

            if ($this->c === 7 && strtolower($t) === 'a') {
                $this->c += 1;
                $this->cache .= $t;

                return false;
            }

            if ($this->c === 8 && $t === '[') {
                $this->c += 1;
                $this->cache .= $t;

                $this->detectedState = static::F_CDATA;

                return false;
            }

            if ($t === '>') {
                $this->c += 1;
                $this->cache .= $t;

                $this->cache = array(
                    'type' => static::F_TAG,
                    'data' => $this->cache
                );

                return true;
            }

            $this->c += 1;
            $this->cache .= $t;

            return false;
        }

        if ($this->detectedState === static::F_CDATA) {

            $this->cdata_end_1 = $this->cdata_end_2;
            $this->cdata_end_2 = $this->cdata_end_3;
            $this->cdata_end_3 = $t;

            $this->c += 1;
            $this->cache .= $t;

            if ($this->cdata_end_1 === ']' && $this->cdata_end_2 === ']' && $this->cdata_end_3 === '>') {

                $this->cache = array(
                    'type' => static::F_CDATA,
                    'data' => $this->cache
                );

                return true;
            }

            return false;
        }

        if ($this->_isWhiteChar($t)) {

            $this->c += 1;
            $this->cache .= $t;

            $this->detectedState = static::F_SPACES;

            return false;
        }

        $this->_setChar($t);

        $this->cache = array(
            'type' => static::F_SPACES,
            'data' => $this->cache
        );

        return true;
    }
    public function key()
    {
        return $this->key;
    }
    public function valid()
    {
        if ($this->cache) {

            return true;
        }

        return false;
    }
    protected function _isNewLine(&$s) {
        return ($s === "\n") || ($s === "\r");
    }
    protected function _isSpace(&$s) {
        return ($s === ' ') || ($s === "\t");
    }
    protected function _isWhiteChar(&$s) {
        return $this->_isSpace($s) || $this->_isNewLine($s);
    }
}