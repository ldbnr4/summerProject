<?php
function getEvents( $zip ){
    $date = date('m/d/Y');
    $3months = date('m/d/Y',strtotime(date("m/d/Y", time()) . " + 120 day"));
    $html = file_get_contents('http://www.jambase.com/shows/Shows.aspx?ArtistID=0&VenueID=0&Zip='.$zip.'&radius=50&StartDate='.$date.'&EndDate='.$3months.'&Rec=False&pagenum=1&pasi=1500');
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
        }
        else if ($col->getAttribute('class') == 'toolCol'){
            $childList = $col->childNodes;
            $link = false;
            foreach ($childList as $child){
                if($child->nodeName == 'a' && $child->hasAttributes()){
                    if($child->getAttribute('target') == 'buy'){
                        $link = true;
                        array_push($event, $child->getAttribute('href'));
                    }
                }
            }
            if($link == false){
                array_push($event, "NULL");
            }
            array_push($events, $event);
        }
    }
    return $events;
}
?>