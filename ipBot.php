<?php
function getLocation( $ip ){
    require_once('simpletest/browser.php');
    $browser = new SimpleBrowser();
    $html = $browser->get('http://www.ipaddresslabs.com/IPGeolocationServiceDemo.do?ipaddress='.$ip.'#StandardEditionTab');
    $dom = new DOMDocument();
    @$dom->loadHTML( $html ); 
    $xpath = new DOMXPath($dom);
    $tableCol = $xpath->query('//td[@*]');
    foreach ($tableCol as $col){
        if (strpos($col->nodeValue, 'postal_code')){
            var_dump($col->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling);
            echo '<hr>';
        }

    }
}
?>