<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

error_reporting(E_ERROR | E_WARNING | E_PARSE);

use App\Event;
use App\Zip;
use App\Artist;
use App\ZipArtist;
use App\ZipEvent;
use App\EventArtist;
use DB;

function JB($zip, $dbZipId){
    echo 'Getting events for '.$zip."\n";
    shell_exec('bash ./getEsPY.sh '.$zip);
    $eString = file_get_contents ('ENV/bin/events.txt');
    if(!is_null($eString) || $eString != 'NULL'){
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
                    echo "Event not in EVENT db. Adding it.\n";
                    $newE = Event::create([    'event' => $e, 
                                               'date' => $date,
                                               'venue' => $ven,
                                               'city' => $city,
                                               'state' => $state,
                                               'tic_url' => $tic_url
                    ]);

                    ZipEvent::create(['event_id' => $newE['id'], 'zip_id' => $dbZipId, 'date' => $date]);
                    if(count($artist) >= 1){
                        echo "There are ".count($artist)." artists for just created event ".$newE['id'].".\n";
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
                            else{
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
                            EventArtist::create(['event_id' => $newE['id'], 'artist_id' => $newArtId, 'date' => $date]);
                        }
                    }
                    else{
                        echo 'There are NO artists for this event.'."\n"; 
                    }
                }
                else{
                    echo "Event is already in event db. Fetching id.\n";
                    $newE = Event::where('event', '=', $e)->get();
                    $newE = $newE->fetch('id');
                    $newE = $newE[0];
                    $newEId = $newE;
                    echo "Checking to see if event ".$newEId." is in the zip_events db with the zip code ".$zip." on ".$date.".\n";
                    $ZEcheck = ZipEvent::where('event_id','=', $newEId)->where('zip_id', '=', $dbZipId)->where('date', '=', $date)->count();
                    if($ZEcheck == 0){
                        echo "Its not so we'll associate event ".$newEId.". with zip:zip_id => ".$zip.":".$dbZipId.".\n";
                        ZipEvent::create(['event_id' => $newEId, 'zip_id' => $dbZipId, 'date' => $date]);
                    }
                    else{
                        echo "Event ".$newEId.". with zip:zip_id => ".$zip.":".$dbZipId." are already associated.\n";
                       //break;
                    }
                    echo "Looking for artists with event id ".$newEId." on ".$date." in event_artists db.\n";
                    $ars = EventArtist::where('event_id', '=', $newEId)->where('date', '=', $date)->get();
                    foreach ($ars as $ar){
                        if(ZipArtist::where('zip_id', '=', $dbZipId)->where('date', '=', $date)->where('artist_id', '=', $ar['artist_id'])->count() == 0){
                            echo "Artist ".$ar['artist_id']." has not been associated with the new zip:zip_id => ".$zip.":".$dbZipId." on ".$date.". Adding to zip_artists db.\n";
                            ZipArtist::create(['artist_id' => $ar['artist_id'],'zip_id' => $dbZipId, 'date' => $date]);
                        }
                        else{
                            echo "Artist ".$ar['artist_id']." has already been associated with the new zip:zip_id => ".$zip.":".$dbZipId." on ".$date.".\n";
                        }
                    }
                }
            }
            echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~Moving on to next event~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
        }
    }
    echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~Done with ".$zip."~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
}

class PythonS extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'Scripts:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        set_time_limit ( 1000000 );
        $file = fopen("cities.csv","r");
        if(DB::table('zips')->count() < 29000){
            while(!feof($file)){  
                $line = (fgetcsv($file));
                if(is_numeric(trim($line[0]))){
                    $zip = trim($line[0]);
                    while(strlen($zip) != 5){
                        $zip = strval($zip);
                        $zip = '0'.$zip;
                    }
                    if(count(Zip::where( 'zipCode', '=', $zip )->get()) == 0){
                    Zip::create(['zipCode' => $zip]);
                    }
                }
            }
        }
        fclose($file);
        Zip::chunk(500, function($zips){
            foreach($zips as $zip){
                $dbZipId = $zip['id'];
                $ZEcheck = DB::table('zip_events')->where('zip_id', '=', $dbZipId)->orderBy('date', 'desc')->first();
                //var_dump ($ZEcheck);             
                
                if(!is_null($ZEcheck)){
                    $date = get_object_vars($ZEcheck);
                    $diff = abs (strtotime(date('Y-m-d')) - strtotime($date['date']));
                    $years = floor($diff / (365*60*60*24));
                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                }
                
                if(is_null($ZEcheck) || $months < 3){
                    if(!(file_exists('ENV'))){
                        shell_exec('bash ./setUpVE.sh');
                    }
                    if(trim(shell_exec('uname')) == 'Lunux'){
                        if(!(file_exists('ENV/lib/python2.6/site-packages/lxml'))){
                            shell_exec('bash ./pipInstals.sh');
                        }
                    }
                    JB($zip['zipCode'], $dbZipId);
                }
                else{
                    echo "No events needed for ".$zip['zipCode']."\n";
                }


            }
        });

        
        $this->info("Events are updated");
        
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			//['example', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
