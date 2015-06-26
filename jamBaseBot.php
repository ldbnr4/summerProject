<?php
function getEvents( $city, $state, $zip ){
    require_once('simpletest/browser.php');
    $browser = new SimpleBrowser();
    $date = 
    $html = $browser->get('http://www.jambase.com/shows/Shows.aspx?ArtistID=0&VenueID=0&City='.$city.'&State='.$state.'&Zip='.$zip.'&radius=50&StartDate=6/25/2015&EndDate=6/25/2016&Rec=False&pagenum=1&pasi=1500');
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
            //echo "<hr>";
            $artists = array();
            $childList = $col->childNodes;
            foreach ($childList as $child){
                //echo ($child->nodeValue);
                if($child->nodeName == "a"){
                    //var_dump($childList);
                    //echo $child->nodeValue;
                    array_push($artists,$child->nodeValue);
                    //$col->parentNode->removeChild($col);
                    //var_dump($childList);
                }
            }
            array_push($event, $artists);
            //print_r($artists);
            //
            //var_dump( $artists );
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