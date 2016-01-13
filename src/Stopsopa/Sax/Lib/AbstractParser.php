<?php

namespace Stopsopa\Sax\Lib;

class AbstractParser
{
    protected $encoding;
    protected $c;
    protected $i;
    protected $data;
    protected $cachei;
    protected $cachec;
    protected $thisIsFirstNotWhiteChar = false;
    protected $canBeFirstNWC = true;

    public function __construct($data, $encoding = 'utf-8')
    {
        $this->setData($data, $encoding);
    }

    public function setData($data, $encoding = 'utf-8')
    {
        $this->data = $data;
        $this->encoding = $encoding;
        $this->c = mb_strlen($data, $encoding);
        $this->i = 0;
    }

    protected function _isFirstCharInLineAndAt(&$s)
    {
        return $s === '@' && $this->_isFirstCharInLine();
    }

    protected function _isFirstCharInLine()
    {
        $this->_getChar();
        return $this->thisIsFirstNotWhiteChar;
    }

    protected function _getChar($o = null, $nextNotWhiteChar = false)
    {
        if ($o || $nextNotWhiteChar) {

            $offset = $this->i + $o;

            if (($offset > $this->c) || ($offset < 0)) {
                return null;
            }

            $s = mb_substr($this->data, $offset, 1, $this->encoding);

            if ($nextNotWhiteChar && $this->_isWhiteChar($s)) {
                $s = $this->_getChar($o + 1, true);
            }

            return $s;
        }

        if ($this->i > $this->c) {
            return false;
        }

        if ($this->cachei !== $this->i) {

            $this->cachei = $this->i;
            $this->cachec = mb_substr($this->data, $this->i, 1, $this->encoding);

            if ($this->thisIsFirstNotWhiteChar) {
                $this->thisIsFirstNotWhiteChar = false;
                $this->canBeFirstNWC = false;
            }

            if ($this->canBeFirstNWC && !$this->_isWhiteChar($this->cachec)) {
                $this->thisIsFirstNotWhiteChar = true;
                $this->canBeFirstNWC = false;
            }

            if ($this->_isNewLine($this->cachec)) {
                $this->thisIsFirstNotWhiteChar = false;
                $this->canBeFirstNWC = true;
            }
        }

        return $this->cachec;
    }

    protected function _isNewLine(&$s)
    {
        return ($s === "\n") || ($s === "\r");
    }

    protected function _isSpace(&$s)
    {
        return ($s === " ") || ($s === "\t");
    }

    protected function _isWhiteChar(&$s)
    {
        return $this->_isSpace($s) || $this->_isNewLine($s);
    }

    protected function _isDelimiter(&$s)
    {
        return ($s === "=") || ($s === ":") || ($s === ",");
    }

    protected function _isForbiddenChar(&$s)
    {
        return $this->_isDelimiter($s) || ($s === "{") || ($s === "(") || ($s === ")") || ($s === "}") || $this->_isWhiteChar($s) || ($s === "@");
    }
}