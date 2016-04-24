<?php

namespace Stopsopa\Sax\Lib;

use Iterator;
use Stopsopa\Sax\Exceptions\SaxException;
use Stopsopa\Sax\Lib\Iterator\StreamTextIterator;

/**
 * Class Sax
 * @package Stopsopa\Sax
 * 160000 tags (3.7mb file size) in 20 sec on vagrant (host i7, ssd disc), hmmm - acceptable
 */
class Sax implements Iterator
{
    const N_SPACES = 1;
    const N_TAG = 2;
    const N_DATA = 3;
    const N_CDATA = 4;
    const N_COMMENT = 5;

//    const N_SPACES = 's';
//    const N_TAG = 't';
//    const N_DATA = 'd';
//    const N_CDATA = 'cd';
//    const N_COMMENT = 'co';

    const MODE_FILE = 1;
    const MODE_STRING = 2;

    protected $iterator;
    protected $key;
    protected $offset;

    protected $detectedState;

    protected $cache;
    protected $cchar;

    protected $c;

    protected $check;

    protected $options;

    public function __construct($source, $options = array())
    {
        if ($options === static::MODE_FILE || $options === static::MODE_STRING) {
            $options = array(
                'mode' => $options
            );
        }

        $this->options = $options = array_merge(array(
            'encoding' => 'utf8',
            'chunk' => null,
            'mode' => null
        ), $options);

        $this->iterator = new StreamTextIterator($options['mode']);
        $this->iterator->initialize($source, $options['encoding'], $options['chunk']);
        $this->rewind();
    }

    protected function _setChar($c)
    {
        $this->cchar = $c;
        return $this;
    }

    protected function _popChar()
    {
        $k = $this->cchar;
        $this->cchar = false;
        return $k;
    }

    protected function _getChar()
    {
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

        $this->detectedState = self::N_SPACES;

        $this->_resetCheck();

        $this->next();
    }

    protected function _resetCheck()
    {
        $this->check = explode('|', str_repeat('|', 7));
    }

    public function current()
    {
        if (is_array($this->cache)) {
            return $this->cache;
        }
    }

    public function next()
    {
        $this->c = 0;

        $this->offset = 0;
        if ($this->key) {
            if ($this->_getChar()) {
                $this->offset = $this->key - 1;
            } else {
                $this->offset = $this->key;
            }
        }

        $this->cache = '';

        $this->check[0] = '';

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

            // if something come to this place, that means it is some kind unclosed tag (tag, comment, or cdata)
            // return this as data
            $this->cache = array(
                'type' => trim($this->cache) ? static::N_DATA : static::N_SPACES,
                'raw' => $this->cache,
                'offset' => $this->offset
            );

        } else {
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

                $this->check[$this->c] = $t;

                $this->c += 1;
                $this->cache .= $t;

                $this->detectedState = static::N_TAG;

                return false;
            }

            if ($this->_isWhiteChar($t)) {
                $this->c += 1;
                $this->cache .= $t;

                $this->detectedState = static::N_SPACES;

                return false;
            }

            $this->c += 1;
            $this->cache .= $t;

            $this->detectedState = static::N_DATA;

            return false;
        }


        switch ($this->detectedState) {

            case static::N_DATA:

                if ($t === '<') {

                    $this->_setChar($t);

                    $this->cache = array(
                        'type' => static::N_DATA,
                        'raw' => $this->cache,
                        'offset' => $this->offset
                    );

                    return true;
                }

                $this->c += 1;
                $this->cache .= $t;

                $this->detectedState = static::N_DATA;

                return false;

            case static::N_TAG:

                $this->cache .= $t;

//            012345678912
//            <!---->
//            <![CDATA[]]>
                if ($this->c < 13) {
                    if ($t === '>') {
                        $this->c += 1;

                        $this->cache = array(
                            'type' => static::N_TAG,
                            'raw' => $this->cache,
                            'data' => $this->_extractData($this->cache, static::N_TAG),
                            'offset' => $this->offset
                        );

                        return true;
                    }
                }

//            <![
//                from
//            <![CDATA[]]>
                if ($this->c < 3) {

                    $this->check[$this->c] = $t;
                    $this->c += 1;

                    return false;
                }

                if ($this->c === 3 && $t === '-') {  //  01234567
//                if (implode('', $this->check) === '<![cdata') {
                    // this way is little faster
                    if ($this->check[0] === '<' && $this->check[1] === '!' && $this->check[2] === '-') {

                        $this->c += 1;

                        $this->detectedState = static::N_COMMENT;

                        return false;
                    }
                }
//               CDATA[
//                from
//            <![CDATA[]]>
                if ($this->c < 8) {

                    $this->check[$this->c] = strtolower($t);
                    $this->c += 1;

                    return false;
                }

                if ($this->c === 8) {  //  01234567
//                if (implode('', $this->check) === '<![cdata') {
                    // this way is little faster
                    if ($t === '[' && $this->check[0] === '<' && $this->check[1] === '!' && $this->check[2] === '[' && $this->check[3] === 'c' && $this->check[4] === 'd' && $this->check[5] === 'a' && $this->check[6] === 't' && $this->check[7] === 'a') {

                        $this->c += 1;

                        $this->detectedState = static::N_CDATA;

                        return false;
                    }
                }

                if ($t === '>') {
                    $this->c += 1;

                    $this->cache = array(
                        'type' => static::N_TAG,
                        'raw' => $this->cache,
                        'data' => $this->_extractData($this->cache, static::N_TAG),
                        'offset' => $this->offset
                    );

                    return true;
                }

                $this->c += 1;

                return false;

            case static::N_CDATA:

                $this->check[0] = $this->check[1];
                $this->check[1] = $this->check[2];
                $this->check[2] = $t;

                $this->c += 1;
                $this->cache .= $t;

                if ($this->check[0] === ']' && $this->check[1] === ']' && $this->check[2] === '>') {

                    $this->cache = array(
                        'type' => static::N_CDATA,
                        'raw' => $this->cache,
                        'data' => $this->_extractData($this->cache, static::N_CDATA),
                        'offset' => $this->offset
                    );

                    return true;
                }

                return false;

            case static::N_COMMENT:

                $this->check[0] = $this->check[1];
                $this->check[1] = $this->check[2];
                $this->check[2] = $t;

                $this->c += 1;
                $this->cache .= $t;

                if ($this->check[0] === '-' && $this->check[1] === '-' && $this->check[2] === '>') {

                    $this->cache = array(
                        'type' => static::N_COMMENT,
                        'raw' => $this->cache,
                        'data' => $this->_extractData($this->cache, static::N_COMMENT),
                        'offset' => $this->offset
                    );

                    return true;
                }

                return false;

        }

        if ($this->_isWhiteChar($t)) {

            $this->c += 1;
            $this->cache .= $t;

            $this->detectedState = static::N_SPACES;

            return false;
        }

        if ($t !== '<') {

            $this->c += 1;
            $this->cache .= $t;

            $this->detectedState = static::N_DATA;

            return false;
        }

        $this->_setChar($t);

        $this->cache = array(
            'type' => static::N_SPACES,
            'raw' => $this->cache,
            'offset' => $this->offset
        );

        return true;
    }

    public function key()
    {
        return $this->offset;
    }

    public function valid()
    {
        if ($this->cache) {

            return true;
        }

        return false;
    }

    protected function _isNewLine(&$s)
    {
        return ($s === "\n") || ($s === "\r");
    }

    protected function _isSpace(&$s)
    {
        return ($s === ' ') || ($s === "\t");
    }

    protected function _isWhiteChar(&$s)
    {
        return $this->_isSpace($s) || $this->_isNewLine($s);
    }

    protected function _extractData($data, $type)
    {
        switch ($type) {
            case static::N_TAG:

                if ($data[1] === '/') { // closing tag
                    return array(
                        'type' => 'closing',
                        'name' => mb_substr($data, 2, -1)
                    );
                }

                // opening tag
                $d = array(
                    'type' => 'opening',
                    'attr' => array()
                );

                $a = &$d['attr'];

                $replace = preg_replace_callback('#\s([^\s]+)\s*=\s*([\'"])([^\\2]*?)\\2#s', function ($m) use (&$a) {
                    if (array_key_exists($m[1], $a)) {
                        if (is_array($a[$m[1]])) {
                            $a[$m[1]][] = $m[3];
                        } else {
                            $a[$m[1]] = array(
                                $a[$m[1]],
                                $m[3]
                            );
                        }
                    } else {
                        $a[$m[1]] = $m[3];
                    }
                    return ' ';
                }, $data);

                $replace = preg_replace_callback('#\s([^\s]+)\s*=\s*([^\s><\/]+)#s', function ($m) use (&$a) {
                    if (array_key_exists($m[1], $a)) {
                        if (is_array($a[$m[1]])) {
                            $a[$m[1]][] = $m[2];
                        } else {
                            $a[$m[1]] = array(
                                $a[$m[1]],
                                $m[2]
                            );
                        }
                    } else {
                        $a[$m[1]] = $m[2];
                    }
                    return ' ';
                }, $replace);

                $replace = preg_split('#\s+#s', rtrim(trim($replace, "/ <>\r\n\t"), '?'));

                for ( $i = 1, $l = count($replace) ; $i < $l ; $i += 1 ) {
                    $m = &$replace[$i];
                    $m = trim($m);
                    if ($m) {
                        if (array_key_exists($m, $a)) {
                            if (is_array($a[$m])) {
                                $a[$m][] = null;
                            } else {
                                $a[$m] = array(
                                    $a[$m],
                                    null
                                );
                            }
                        } else {
                            $a[$m] = null;
                        }
                    }
                }

                $d['name'] = '';
                if (!empty($replace[0])) {
                    $d['name'] = $replace[0];
                }

                // checking if tag is empty
                $data = rtrim($data, ">\r\n\t ");
                $l = strlen($data);
                $data = rtrim($data, "/");
                if ($l !== strlen($data)) {
                    $d['type'] = 'empty';
                }

                return $d;
            case static::N_CDATA:
//                0123456789
//                <![CDATA[]]>
                return mb_substr($data, 9, -3, $this->options['encoding']);
            case static::N_COMMENT:
//                0123456789
//                <!--
                return mb_substr($data, 4, -3, $this->options['encoding']);
        }
    }
    public function setCache($cache) {
        $this->cache = $cache;
        return $this;
    }
}