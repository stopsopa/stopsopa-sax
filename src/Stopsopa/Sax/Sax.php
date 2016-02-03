<?php

namespace Stopsopa\Sax;

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
    const F_SPACES = 1;
    const F_TAG = 2;
    const F_DATA = 3;
    const F_CDATA = 4;
    const F_COMMENT = 5;

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

        $this->detectedState = self::F_SPACES;

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

            $this->cache = array(
                'type' => $this->detectedState,
                'raw' => $this->cache
            );

            if (in_array($this->detectedState, array(static::F_TAG, static::F_CDATA))) {
                $this->cache['data'] = $this->_extractData($this->cache, $this->detectedState);
            }

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


        switch ($this->detectedState) {

            case static::F_DATA:

                if ($t === '<') {

                    $this->_setChar($t);

                    $this->cache = array(
                        'type' => static::F_DATA,
                        'raw' => $this->cache,
                        'offset' => $this->offset
                    );

                    return true;
                }

                $this->c += 1;
                $this->cache .= $t;

                $this->detectedState = static::F_DATA;

                return false;

            case static::F_TAG:

                $this->cache .= $t;

//            012345678912
//            <!---->
//            <![CDATA[]]>
                if ($this->c < 13) {
                    if ($t === '>') {
                        $this->c += 1;

                        $this->cache = array(
                            'type' => static::F_TAG,
                            'raw' => $this->cache,
                            'data' => $this->_extractData($this->cache, static::F_TAG),
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

                        $this->detectedState = static::F_COMMENT;

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

                        $this->detectedState = static::F_CDATA;

                        return false;
                    }
                }

                if ($t === '>') {
                    $this->c += 1;

                    $this->cache = array(
                        'type' => static::F_TAG,
                        'raw' => $this->cache,
                        'data' => $this->_extractData($this->cache, static::F_TAG),
                        'offset' => $this->offset
                    );

                    return true;
                }

                $this->c += 1;

                return false;

            case static::F_CDATA:

                $this->check[0] = $this->check[1];
                $this->check[1] = $this->check[2];
                $this->check[2] = $t;

                $this->c += 1;
                $this->cache .= $t;

                if ($this->check[0] === ']' && $this->check[1] === ']' && $this->check[2] === '>') {

                    $this->cache = array(
                        'type' => static::F_CDATA,
                        'raw' => $this->cache,
                        'data' => $this->_extractData($this->cache, static::F_CDATA),
                        'offset' => $this->offset
                    );

                    return true;
                }

                return false;

            case static::F_COMMENT:

                $this->check[0] = $this->check[1];
                $this->check[1] = $this->check[2];
                $this->check[2] = $t;

                $this->c += 1;
                $this->cache .= $t;

                if ($this->check[0] === '-' && $this->check[1] === '-' && $this->check[2] === '>') {

                    $this->cache = array(
                        'type' => static::F_COMMENT,
                        'raw' => $this->cache,
                        'data' => $this->_extractData($this->cache, static::F_COMMENT),
                        'offset' => $this->offset
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

    protected function _extractData(&$data, $type)
    {

        switch ($type) {
            case static::F_TAG:

                if ($data[1] === '/') { // closing tag
                    return array(
                        'type' => 'closing',
                        'name' => mb_substr($data, 2, -1)
                    );
                }

                // opening tag
                $d = array(
                    'type' => 'opening'
                );

                preg_match('#^<\s*([^\s>"\']*)(?:\s+(.*))?#is', $data, $m);

                $d['name'] = '';
                if (!empty($m[1])) {
                    $d['name'] = $m[1];
                }

                $d['attr'] = array();
                if (!empty($m[2])) {

//                    preg_match_all('#\s([a-z0-9_\-:\?]+)(=([\'"])([^\\3]*?)\\3)?#i', $m[0], $attrs);
                    preg_match_all('#\s([^\s]+)(=([\'"])([^\\3]*?)\\3)?#i', $m[0], $attrs);

                    if (isset($attrs[0]) && is_array($attrs[0])) {

                        $d['attr'] = array();

                        foreach ($attrs[0] as $attr) {
                            if ($attr[0] !== '<') {

                                $name = null;
                                $value = null;

                                $split = mb_split('=', $attr, 2);

                                $name = trim($split[0], '?/> \r\n\t');

                                if (isset($split[1])) {

                                    $value = trim($split[1], '?/> \r\n\t');

                                    switch ($value[0]) {
                                        case '"':
                                            $value = trim($value, '"');
                                            break;
                                        case "'":
                                            $value = trim($value, "'");
                                            break;
                                    }
                                }

                                if (!$name) {
                                    continue;
                                }

                                if (array_key_exists($name, $d['attr'])) {
                                    if (is_array($d['attr'][$name])) {
                                        $d['attr'][$name][] = $value;
                                    } else {

                                        $d['attr'][$name] = array(
                                            $d['attr'][$name],
                                            $value
                                        );
                                    }
                                } else {
                                    $d['attr'][$name] = $value;
                                }
                                $aaaa = $d['attr'];
                                $aaaa = $aaaa;
                            }
                        }
                    }
                }

                // checking if tag is empty
                $data = rtrim($data, '>');
                $l = strlen($data);
                $data = rtrim($data, '/');
                if ($l !== strlen($data)) {
                    $d['type'] = 'empty';
                }

                return $d;
            case static::F_CDATA:
//                0123456789
//                <![CDATA[]]>
                return mb_substr($data, 9, -3, $this->options['encoding']);
            case static::F_COMMENT:
//                0123456789
//                <!--
                return mb_substr($data, 4, -3, $this->options['encoding']);
        }
    }
}