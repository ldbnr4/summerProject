<?php
function getLocation( $ip ){
    $html = file_get_contents('http://whatismyipaddress.com/ip/'.$ip); 
    $dom = new DOMDocument();
    @$dom->loadHTML( $html ); 
    $xpath = new DOMXPath($dom);
    $tableCol = $xpath->query('//th[@*]');
    $ret = array();
    foreach ($tableCol as $col){
        if (strpos($col->nodeValue, 'State/Region:')){
            $val = $col->nextSibling;
            if(!in_array($val,$ret)){
                array_push($ret,$val);
            }
        }
        else if (strpos($col->nodeValue, 'City:')){
            $val = $col->nextSibling;
            if(!in_array($val,$ret)){
                array_push($ret,$val);
            }
            
        }
        if (strpos($col->nodeValue, 'Postal Code:')){
            $val = $col->nextSibling;
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