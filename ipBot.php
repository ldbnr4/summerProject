<?php
//require_once('../vamos/simpletest/browser.php');
//echo 'made it to ipbot';
//require_once('simpletest/browser.php');
//echo 'made it to outside function';
function getLocation( $ip ){
    //$browser = new SimpleBrowser();
    //$html = $browser->get('http://www.ipaddresslabs.com/IPGeolocationServiceDemo.do?ipaddress='.$ip.'#StandardEditionTab');
    //echo 'made it inside function';
    $html = file_get_contents('http://www.ipaddresslabs.com/IPGeolocationServiceDemo.do?ipaddress='.$ip.'#StandardEditionTab'); 
    $dom = new DOMDocument();
    @$dom->loadHTML( $html ); 
    $xpath = new DOMXPath($dom);
    $tableCol = $xpath->query('//td[@*]');
    $ret = array();
    foreach ($tableCol as $col){
        if (strpos($col->nodeValue, 'postal_code')){
            $val = $col->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->textContent;
            if(!in_array($val,$ret)){
                array_push($ret,$val);
            }
        }
        else if (strpos($col->nodeValue, 'city')){
            if($col->nextSibling->nextSibling->nextSibling->nextSibling){
                $val = $col->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->textContent;
                if(!in_array($val,$ret)){
                    array_push($ret,$val);
                }
            }
        }
        if (strpos($col->nodeValue, 'region_code')){
            $val = $col->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->textContent;
            if(!in_array($val,$ret)){
                array_push($ret,$val);
            }
        }
        if (strpos($col->nodeValue, 'region_name')){
            $val = $col->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->textContent;
            if(!in_array($val,$ret)){
                array_push($ret,$val);
            }
        }
    }
    return $ret;
}
function getZip( $ip ){
    $browser = new SimpleBrowser();
    $html = $browser->get('http://www.ipaddresslabs.com/IPGeolocationServiceDemo.do?ipaddress='.$ip.'#StandardEditionTab');
    $dom = new DOMDocument();
    @$dom->loadHTML( $html ); 
    $xpath = new DOMXPath($dom);
    $tableCol = $xpath->query('//td[@*]');
    foreach ($tableCol as $col){
        if (strpos($col->nodeValue, 'postal_code')){
            $val = $col->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->textContent;
            return $val;
        }
    }
}
?>