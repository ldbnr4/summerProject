<?php  namespace App\Models;
    //use Illuminate\Database\Eloquent\Model;
    //use App\Event;
    //use Artist;
//use DB;

    $completeEsArray = array();
    echo shell_exec('bash ./getEsPY.sh '.$argv[1]);
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
                $indivE = array();
                $artArray = array();
                $indivE['artists'] = array();
                $indivE['date'] = $date;
                $indivE['venue'] = $ven;
                $indivE['city'] = $city;
                $indivE['state'] = $state;
                $indivE['tic_url'] = $tic_url;
                $echeck = Event::where('event', '=', $e)->count();
                if ($echeck == 0){
                    $newE = Event::create([    'event' => $e, 
                                               'date' => $date,
                                               'venue' => $ven,
                                               'city' => $city,
                                               'state' => $state,
                                               'tic_url' => $tic_url
                    ]);
                    if(count($artist) >= 1){
                        foreach($artist as $art){
                            $art = trim($art);
                            if($art == ''){
                                $art = "Unkown";
                            }
                            $artArray['name'] = $art;
                            $newArt = Artist::where( 'name', '=', $art);
                            if($newArt->count() == 0){
                                shell_exec('bash ./getPicPY.sh '.urlencode($art));
                                $pic_url = file_get_contents('ENV/bin/pic.txt');
                                if(is_null($pic_url) || $pic_url == ''){
                                    $pic_url = 'pics/concert.jpg';
                                }
                                $artArray['pic_url'] = $pic_url;
                                Artist::create([  'name' => $art, 'pic_url' => $pic_url]);
                            }
                            else{
                                $newArt = $newArt->get();
                                $newArt = $newArt[0];
                                $artArray['pic_url'] = $newArt['pic_url'];
                            }
                        }
                    }
                    $indivE['artists'] = $artist;
                }

            }
            array_push($completeEsArray,$indivE);
        }
    }
    else{
        $f = fopen("bad_zips.txt","a");
        fwrite($f,$argv[1]." ");
        fclose($f);
    }
    return $completeEsArray;
?>