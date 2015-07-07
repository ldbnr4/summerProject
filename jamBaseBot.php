<?php
function getEvents( $zip ){
    //require_once('simpletest/browser.php');
    //require_once('../vamos/simpletest/browser.php');
    //$browser = new SimpleBrowser();
    $date = date('m/d/Y');
    $oneYearOn = date('m/d/Y',strtotime(date("m/d/Y", time()) . " + 180 day"));
    //$html = $browser->get('http://www.jambase.com/shows/Shows.aspx?ArtistID=0&VenueID=0&Zip='.$zip.'&radius=50&StartDate='.$date.'&EndDate='.$oneYearOn.'&Rec=False&pagenum=1&pasi=1500');
    $html = file_get_contents('http://www.jambase.com/shows/Shows.aspx?ArtistID=0&VenueID=0&Zip='.$zip.'&radius=50&StartDate='.$date.'&EndDate='.$oneYearOn.'&Rec=False&pagenum=1&pasi=1500');
    $dom = new DOMDocument();
    @$dom->loadHTML( $html ); 
    $xpath = new DOMXPath($dom);
    $tableCol = $xpath->query('//td[@*]');
    $events = array();
    foreach ($tableCol as $col){
        if ($col->parentNode->getAttribute('class') == 'dateRow'){
            $date = explode(" ",$col->nodeValue);
            $date = $date[0];
        }
        if ($col->getAttribute('class') == 'artistCol'){
            $event = array();
            array_push($event, $date);
            $artists = array();
            $childList = $col->childNodes;
            foreach ($childList as $child){
                if($child->nodeName == "a"){
                    array_push($artists,$child->nodeValue);
                }
            }
            array_push($event, $artists);
        }
        else if ($col->getAttribute('class') == 'venueCol'){
            array_push($event, $col->nodeValue);
        }
        else if ($col->getAttribute('class') == 'locationCol'){
            $i=0;
            $childList = $col->childNodes;
            foreach ($childList as $child){
                if($child->nodeName == "a" && $i==0){
                    $location = $child->nodeValue.",";
                    $i++;
                }
                else if($child->nodeName == "a" && $i==1){
                    $location = $location." ".$child->nodeValue;
                    array_push($event, $location);
                }
            }
            array_push($events, $event);
        }
    }
    return $events;
}
?>