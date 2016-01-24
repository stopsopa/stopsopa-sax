<?php

$root = __DIR__.'/../../../../..';


require_once "$root/vendor/autoload.php";

$file = "$root/src/Stopsopa/Sax/Tests/Lib/Iterator/files/unicode.txt";

$i = new \Stopsopa\Sax\Lib\Iterator\StreamTextIterator();
$i->initialize($file);


while ($i->next()) {

}