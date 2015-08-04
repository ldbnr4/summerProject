<?php    
    function JB($zip){
        echo 'Getting events for '.$zip."\n";
        shell_exec('bash ./getEsPY.sh '.$zip);
        $eString = file_get_contents ('ENV/bin/events.txt');
        if(strlen($eString) > 2 && $eString != 'NULL'){
            $eArray = explode('|', $eString);
            $eArray = str_replace('[', '',$eArray);
            $eArray = str_replace(']', '',$eArray);
            unset($eArray[0]);
            foreach ($eArray as $eventA){
                $event = explode(';', $eventA);
                unset($event[0]);
                $event = str_replace("', '", '', $event);
                $event = str_replace("\", \"", '', $event);
                $event = str_replace("', \"", '', $event);
                $event = str_replace("\", '", '', $event);
                $event[1] = str_replace("', u'", '', $event[1]);
                $event[6] = str_replace(" ", '', $event[6]);
                $event[6] = str_replace("'", '', $event[6]);
                $e = trim(serialize($event));
                $dateBuf = strtotime(trim($event[1]));
                $date = date('Y-m-d', $dateBuf);
                $ven = trim($event[3]);
                $city = trim($event[4]);
                $state = trim($event[5]);
                $tic_url = trim($event[6]);
                if( count($event) == 6 ){
                    $artist = explode(':', $event[2]);
                    /*Event::chunk(500, function($dbEvents){
                        foreach($dbEvents as $dbE){
                            $dbE['event'] = trim(serialize($event))
                        }
                    });*/
                    echo "Checking EVENT db for event.\n";
                    $echeck = Event::where('event', '=', $e)->count();
                    if ($echeck == 0){
                        /*echo "Event not in EVENT db. Adding it.\n";
                        $newE = Event::create([    'event' => $e, 
                                                   'date' => $date,
                                                   'venue' => $ven,
                                                   'city' => $city,
                                                   'state' => $state,
                                                   'tic_url' => $tic_url
                        ]);

                        ZipEvent::create(['event_id' => $newE['id'], 'zip_id' => $dbZipId, 'date' => $date]);*/
                        if(count($artist) >= 1){
                            //echo "There are ".count($artist)." artists for just created event ".$newE['id'].".\n";
                            foreach($artist as $art){
                                $art = trim($art);
                                if($art == ''){
                                    $art = "Unkown";
                                }
                                echo "Looking for ".$art." in ARTIST db.\n";
                                $newArt = Artist::where( 'name', '=', $art);
                                if($newArt->count() == 0){
                                    echo "New artist. Getting photo of ".$art.".\n";
                                    shell_exec('bash ./getPicPY.sh '.urlencode($art));
                                    $pic_url = file_get_contents('ENV/bin/pic.txt');
                                    if(is_null($pic_url) || $pic_url == ''){
                                        $pic_url = 'pics/concert.jpg';
                                    }
                                    $newArt = Artist::create([  'name' => $art, 'pic_url' => $pic_url]);
                                    $newArtId = $newArt['id'];
                                }
                                /*else{
                                    echo 'Already have '.$art." in the databse. Fetching id.\n";
                                    $newArt = $newArt->get();
                                    $newArt = $newArt->fetch('id');
                                    $newArt = $newArt[0];
                                    $newArtId = $newArt;

                                }
                                echo 'Checking to see if '.$art." has been in ".$zip." already on ".$date.".\n";
                                $ZAcheck = ZipArtist::where('artist_id', '=', $newArtId)->where('zip_id', '=', $dbZipId)->where('date', '=', $date)->count();
                                if($ZAcheck == 0){
                                    echo $art." has not been in ".$zip." on ".$date.". Adding this.\n";
                                    ZipArtist::create(['artist_id' => $newArtId,'zip_id' => $dbZipId, 'date' =>$date]);
                                }
                                else{
                                    echo $art." has been in ".$zip." on ".$date.".\n";
                                }
                                EventArtist::create(['event_id' => $newE['id'], 'artist_id' => $newArtId, 'date' => $date]);*/
                            }
                        }
                    }

                }
            }
        }
        else{
            $f = fopen("bad_zips.txt","a");
            fwrite($f,$zip." ");
            fclose($f);
        }
    }
?>