<?php

namespace Stopsopa\Sax;

use Iterator;
use Stopsopa\Sax\Exceptions\SaxException;
use Stopsopa\Sax\Lib\AbstractParser;

class Sax extends AbstractParser implements Iterator
{
    /**
     * start tag
     */
    const STATE_BETWEEN     = 0x1000; /* before or after html or between tags, spaces/newlines area */
    /**
     * start tag
     */
    const STATE_TAG         = 0x0100; /* html tag, opening or closing */
    /**
     * data
     */
    const STATE_DATA        = 0x0010;
    /**
     * cdata
     */
    const STATE_CDATA       = 0x0001;

    protected $state;
    protected $tag;

    public function setData($data, $encoding = 'utf-8')
    {
        parent::setData($data, $encoding);
        $this->state = static::STATE_TAG;
    }

    /**
     *
        rewind
        valid
        current
        next
        valid
        current
        next
        valid if return false, end


     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->_getChar();
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        if (mb_strpos($this->data, '<', $this->i, $this->encoding) > $this->i) {
            $data = $this->_collectToWithoutChar('<');
//            if () {
//
//            }
            return array(
                'data' => $data,
                'type' => static::STATE_BETWEEN
            );
        }
//        if ($this->state === static::STATE_BETWEEN) {
//            if ($this->_getChar() === '<') {
//                return $this->_parseTag();
//            }
//            return $this->_parseBetween();
//        }
//        switch ($this->state) {
//            case static::STATE_BETWEEN:
//                if ($this->_getChar() === '<') {
//                    $this->state = static::STATE_TAG;
//                    continue;
//                }
//                $data = $this->_collectToWithoutChar('<');
//                $this->tag = array(
//                    'data' => $this->_collectToWithoutChar('<'),
//                    'type' => static::STATE_BETWEEN
//                );
//                break;
//            case static::STATE_TAG:
//                break;
//            case static::STATE_DATA:
//                break;
//            case static::STATE_CDATA:
//                break;
//        }
//        while (is_string($s = $this->_getChar())) {
//
//        }
    }

    /**
     * starts from current char, collect all chars to given char but without this char.
     * @param $char
     * @return bool|string
     */
    protected function _collectToWithoutChar($char) {

        $ret = '';

        while (is_string($s = $this->_getChar())) {

            if ($s === $char) {
                return $ret;
            }

            $this->i += 1;
            $ret .= $s;
        }

        return $ret;
    }
    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->i;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->i < $this->c;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->_o('test');
        $this->_o($this->_collectToWithoutChar('<'));
        $this->_d();
        die('koniec c');
        if ($this->encoding) {
            return $this->setData($this->data, $this->encoding);
        }

        throw new SaxException("Can't rewind - first setup object using __construct or setData methods");
    }
}