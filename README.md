[![Build Status](https://travis-ci.org/stopsopa/stopsopa-sax.svg?branch=master)](https://travis-ci.org/stopsopa/stopsopa-sax)
[![Codeship Status for stopsopa/stopsopa-sax](https://codeship.com/projects/0f4debe0-9ec5-0133-8d32-76efcd0f79bd/status?branch=master)](https://codeship.com/projects/127803)
[![Coverage Status](https://coveralls.io/repos/stopsopa/stopsopa-sax/badge.svg?branch=master&service=github)](https://coveralls.io/github/stopsopa/stopsopa-sax?branch=master)
[![Latest Stable Version](https://poser.pugx.org/stopsopa/sax/v/stable)](https://packagist.org/packages/stopsopa/sax)
[![License](https://poser.pugx.org/stopsopa/sax/license)](https://packagist.org/packages/stopsopa/sax)



<h1>stopsopa/sax</h1>

Lightweight SAX parser/iterator


***


## Instalation

Follow packagist instructions: [Packagist](https://packagist.org/packages/stopsopa/sax)

## Usage of library: 

### In file mode:

    use Stopsopa\Sax\Sax;
    
    $s = new Sax($path_to_file_witch_xml_or_html, array(
        'mode' => Sax::MODE_FILE
    ));
    // or
    $s = new Sax($path_to_file_witch_xml_or_html, Sax::MODE_FILE);
    
    foreach ($s as $d) {
        if ($d['type'] === Sax::N_TAG) {
            // iterates through nodes of xml/html
        }
    }

### In string mode:

    use Stopsopa\Sax\Sax;
    
    $s = new Sax($xml_or_html_as_a_string, array(
        'mode' => Sax::MODE_STRING
    ));
    // or
    $s = new Sax($xml_or_html_as_a_string, Sax::MODE_STRING);
    
    foreach ($s as $d) {
        if ($d['type'] === Sax::N_TAG) {
            // iterates through nodes of xml/html
        }
    }
    
## Nodes types:

### Possible types witch their json representation:

-   Sax::N_TAG 
    
***   
        
**node example** __(opening tag)__: 
    
    <note id="eid" class="red blue" data-at="one" data-at="two" data-at>
      
      
**data example** __(opening tag)__:
             
      
    {
        "type": 2,
        "raw": "<note id=\"eid\" class=\"red blue\" data-at=\"one\" data-at=\"two\" data-at>",
        "data": {
            "type": "opening",
            "attr": {
                "id": "eid",
                "class": "red blue",
                "data-at": [
                    "one",
                    "two",
                    null
                ]
            },
            "name": "note"
        },
        "offset": 0
    }
    
***    
      
**node example** __(closing tag)__: 

        </note>
      
      
**data example** __(closing tag)__: 
        
    {
        "type": 2,
        "raw": "<\/note>",
        "data": {
            "type": "closing",
            "name": "note"
        },
        "offset": 0
    } 
    
***   
      
**node example** __(empty tag)__: 

    <div data="empty" />
      
      
**data example** __(empty tag)__: 

    {
        "type": 2,
        "raw": "<div data=\"empty\" \/>",
        "data": {
            "type": "empty",
            "attr": {
                "data": "empty"
            },
            "name": "div"
        },
        "offset": 0
    }
    
***    
         
-   Sax::N_DATA   __(data between xml/html nodes)__    

**data example**:  
    
    <div> 
    value        
    </div>
        
**data example**: 
 
    {
        "type": 3,
        "raw": "\r\nvalue\r\n",
        "offset": 5
    }
 
-   Sax::N_CDATA 

**node example**: 

    <![CDATA[test data]>]]>
      
      
**data example**: 

    {
        "type": 4,
        "raw": "<![CDATA[test data]>]]>",
        "data": "test data]>",
        "offset": 5
    }  
    
-   Sax::N_COMMENT

**data example**:  

    <!-- comment -->
        
**data example**: 

    {
        "type": 5,
        "raw": "<!-- comment -->",
        "data": " comment ",
        "offset": 0
    }
  
-   Sax::N_SPACES __(spaces between xml/html nodes)__            
        
**data example**:        
        
    {
        "offset": 38, 
        "raw": "\\r\\n", 
        "type": 1
    }    

### Iteration:

    foreach ($sax_instance as $d) {
        switch ($d['']) {
            case Sax::N_TAG:
                    // do something witch decomposed tag
                break;
            case Sax::N_DATA:
                    // do something witch decomposed data between tag
                break;
            ... etc.
        }
    }

    
## Additional options: 

    $s = new Sax($pathtofile, array(
        'encoding' => 'utf8',
        'chunk' => null, // default 1024
        'mode' => null // default Sax::MODE_FILE, can be also Sax::MODE_STRING
    ));

### License

The MIT License (MIT) <br />
Copyright (c) 2016 Szymon Działowski <br />
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

