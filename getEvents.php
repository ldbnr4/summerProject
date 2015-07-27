<?php
include "HEADER.php"
    
    function JB ($zip, $dbZipId){
        shell_exec('bash ./mid.sh '.$zip);
        $eString = file_get_contents ('ENV/bin/events.txt');
        if($eString != 'NULL'){
            $eArray = explode('|', $eString);
            $eArray = str_replace('[', '',$eArray);
            $eArray = str_replace(']', '',$eArray);
            unset($eArray[0]);
            foreach ($eArray as $eventA){
                $event = explode(';', $eventA);
                unset($event[0]);
                $event = str_replace("', '", '', $event);
                $event[1] = str_replace("', u'", '', $event[1]);
                $event[6] = str_replace(" ", '', $event[6]);
                $event[6] = str_replace("'", '', $event[6]);
                $event[3] = str_replace("', \"", '', $event[3]);
                $event[3] = str_replace("\", '", '', $event[3]);
                $event[4] = str_replace("', \"", '', $event[4]);
                $event[4] = str_replace("\", '", '', $event[4]);
                $event[2] = str_replace("', \"", '', $event[2]);
                $event[2] = str_replace("\", '", '', $event[2]);
                $e = trim(serialize($event));
                $date = trim($event[1]);
                $ven = trim($event[3]);
                $city = trim($event[4]);
                $state = trim($event[5]);
                $tic_url = trim($event[6]);
                if( count($event) == 6 ){
                    $artist = explode(':', $event[2]);
                    $echeck = Event::where('event', '=', trim(serialize($event)))->count();
                    if ($echeck == 0){
                        $newE = Event::create([    'event' => $e, 
                                                   'date' => $date,
                                                   'venue' => $ven,
                                                   'city' => $city,
                                                   'state' => $state,
                                                   'tic_url' => $tic_url
                        ]);
                        $ZEcheck = ZipEvent::where('event_id','=', $newE['id'])->where('zip_id', '=', $dbZipId)->where('date', '=', $date)->count();
                        if($ZEcheck == 0){
                            ZipEvent::create(['event_id' => $newE['id'], 'zip_id' => $dbZipId, 'date' => $date]);
                        }
                        if(count($artist) >= 1){
                            foreach($artist as $art){
                                $art = trim($art);
                                $newArt = Artist::where( 'name', '=', $art);
                                if($newArt->count() == 0){
                                    shell_exec('bash ./mid.sh '.urlencode($art));
                                    $pic_url = file_get_contents('ENV/bin/pic.txt');
                                    if(is_null($pic_url)){
                                        $pic_url = 'pics/concert.jpg';
                                    }
                                    $newArt = Artist::create([  'name' => $art, 'pic_url' => $pic_url]);
                                    $newArtId = $newArt['id'];
                                }
                                else{
                                    $newArt = $newArt->get();
                                    $newArt = $newArt->fetch('id');
                                    $newArt = $newArt[0];
                                    $newArtId = $newArt;

                                }
                                $ZAcheck = ZipArtist::where('artist_id', '=', $newArtId)->where('zip_id', '=', $dbZipId)->where('date', '=', $date)->count();
                                if($ZAcheck == 0){
                                    ZipArtist::create(['artist_id' => $newArtId,'zip_id' => $dbZipId, 'date' =>$date]);
                                }
                                $EAcheck = EventArtist::where('artist_id', '=', $newArtId)->where('event_id', '=', $newE['id'])->where('date', '=', $date)->count();
                                if($EAcheck == 0){
                                     EventArtist::create(['event_id' => $newE['id'], 'artist_id' => $newArtId, 'date' => $date]);
                                }
                            }
                        }
                    }
                    else{
                        $newE = Event::where('event', '=', $e)->get();
                        $newE = $newE->fetch('id');
                        $newE = $newE[0];
                        $newEId = $newE;

                        $ZEcheck = ZipEvent::where('event_id','=', $newEId)->where('zip_id', '=', $dbZipId)->where('date', '=', $date)->count();
                        if($ZEcheck == 0){
                            ZipEvent::create(['event_id' => $newEId, 'zip_id' => $dbZipId, 'date' => $date]);
                        }
                        $ars = EventArtist::where('event_id', '=', $newEId)->where('date', '=', $date);
                        foreach ($ars as $ar){
                            ZipArtist::create(['artist_id' => $ar['id'],'zip_id' => $dbZipId, 'date' => $date]);
                        }

                    }
                }
            }
        }
    }
?>