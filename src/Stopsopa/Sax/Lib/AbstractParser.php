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

    protected $offsettmp;
    /*
     * Z metodą pracuje się tak że można pobrać dowolny znak
     * z uwzględnieniem przesunięcia podanego w $o (jeśli brak to zwraca z pod
     * wskaźnika $this->i). Samo pobranie z użyciem tej metody nie powoduje przesunięcia tego wskaźnika
     * Jednoszcześnie testowany wskąźnik zapisywany jest w $this->offsettmp
     *
     */
    /**
     * @param null $o
     * @param bool $nextNotWhiteChar  true - zwraca pierwzy nie biały znak,
     * @return bool|null|string
     */
    protected function _getChar($o = null, $nextNotWhiteChar = false) {

        if ($o || $nextNotWhiteChar) {
            $this->offsettmp = $this->i + $o;

            if ( ($this->offsettmp > $this->c) || ($this->offsettmp < 0) )
                return null;

            $s = mb_substr($this->data, $this->offsettmp, 1);

            if ($nextNotWhiteChar && $this->_isWhiteChar($s))
                $s = $this->_getChar($o+1, true);

            return $s;
        }

        if ($this->i > $this->c)
            return false;

        if ($this->cachei !== $this->i) {
            $this->cachei = $this->i;
            $this->cachec = mb_substr($this->data, $this->i, 1);

//            if ($this->thisIsFirstNotWhiteChar) {
//                $this->thisIsFirstNotWhiteChar = false;
//                $this->canBeFirstNWC = false;
//            }
//
//            if ($this->canBeFirstNWC && !$this->_isWhiteChar($this->cachec)) {
//                $this->thisIsFirstNotWhiteChar = true;
//                $this->canBeFirstNWC = false;
//            }
//
//            if ($this->_isNewLine($this->cachec)) {
//                $this->thisIsFirstNotWhiteChar = false;
//                $this->canBeFirstNWC = true;
//            }

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
    protected function _d($c = '') {
        ob_start();
        var_dump('<'.$this->_f($this->_getChar()).'>'. $this->_f(mb_substr($this->data, $this->i, 60)).'<');
        $d = ob_get_clean();

        fwrite(STDOUT, $d);
    }
    protected function _f($k) {
        return str_replace("\r",'\r',str_replace("\n",'\n',$k));
    }

    protected function _o($data) {
        ob_start();
        var_dump($data);
        $d = ob_get_clean();
        fwrite(STDOUT, $d);
    }
//    protected function _isDelimiter(&$s)
//    {
//        return ($s === "=") || ($s === ":") || ($s === ",");
//    }

//    protected function _isForbiddenChar(&$s)
//    {
//        return $this->_isDelimiter($s) || in_array($s, '<','>') || $this->_isWhiteChar($s);
//    }
}