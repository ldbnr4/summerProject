<?php
function getLocation( $ip ){
    require_once('../vamos/simpletest/browser.php');
    $browser = new SimpleBrowser();
    $html = $browser->get('http://www.ipaddresslabs.com/IPGeolocationServiceDemo.do?ipaddress='.$ip.'#StandardEditionTab');
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
        if (strpos($col->nodeValue, 'region_name')){
            $val = $col->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->textContent;
            if(!in_array($val,$ret)){
                array_push($ret,$val);
            }
        }
    }
    return $ret;
}
?>