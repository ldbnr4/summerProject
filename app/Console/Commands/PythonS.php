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
                echo "Checking db for existing event.\n";
                $echeck = Event::where('event', '=', trim(serialize($event)))->count();
                if ($echeck == 0){
                    echo "Event not in db. Adding it.\n";
                    $newE = Event::create([    'event' => $e, 
                                               'date' => $date,
                                               'venue' => $ven,
                                               'city' => $city,
                                               'state' => $state,
                                               'tic_url' => $tic_url
                    ]);

                    ZipEvent::create(['event_id' => $newE['id'], 'zip_id' => $dbZipId, 'date' => $date]);
                    if(count($artist) >= 1){
                        echo "There are artists for this event.\n";
                        foreach($artist as $art){
                            $art = trim($art);
                            $newArt = Artist::where( 'name', '=', $art);
                            if($newArt->count() == 0){
                                echo "Getting photo of ".$art.".\n";
                                shell_exec('bash ./getPicPY.sh '.urlencode($art));
                                $pic_url = file_get_contents('ENV/bin/pic.txt');
                                if(is_null($pic_url)){
                                    $pic_url = 'pics/concert.jpg';
                                }
                                $newArt = Artist::create([  'name' => $art, 'pic_url' => $pic_url]);
                                $newArtId = $newArt['id'];
                            }
                            else{
                                echo 'Already have '.$art." in the databse.\n";
                                $newArt = $newArt->get();
                                $newArt = $newArt->fetch('id');
                                $newArt = $newArt[0];
                                $newArtId = $newArt;

                            }
                             echo 'Checking to see if '.$art." has been in ". $zip." already on ".$date.".\n";
                            $ZAcheck = ZipArtist::where('artist_id', '=', $newArtId)->where('zip_id', '=', $dbZipId)->where('date', '=', $date)->count();
                            if($ZAcheck == 0){
                                echo $art." has not. Adding this.\n";
                                ZipArtist::create(['artist_id' => $newArtId,'zip_id' => $dbZipId, 'date' =>$date]);
                            }
                            else{
                                echo $art." has already.\n";
                            }
                            EventArtist::create(['event_id' => $newE['id'], 'artist_id' => $newArtId, 'date' => $date]);
                        }
                    }
                    else{
                        echo 'There are NO artists for this event.'."\n"; 
                    }
                }
                else{
                    echo "Event already in db. Getting it now.\n";
                    $newE = Event::where('event', '=', $e)->get();
                    $newE = $newE->fetch('id');
                    $newE = $newE[0];
                    $newEId = $newE;
                    echo "Checking to see if this event is in the db with the zip code ".$zip.".\n";
                    $ZEcheck = ZipEvent::where('event_id','=', $newEId)->where('zip_id', '=', $dbZipId)->where('date', '=', $date)->count();
                    if($ZEcheck == 0){
                        echo "Its not so we'll add it.\n";
                        ZipEvent::create(['event_id' => $newEId, 'zip_id' => $dbZipId, 'date' => $date]);
                    }
                    else{
                        echo "It is so dont add it.\n";
                    }
                    echo "Looking for artists with event id ".$newEId." on ".$date.".\n";
                    $ars = EventArtist::where('event_id', '=', $newEId)->where('date', '=', $date)->get();
                    //var_dump($ars);
                    foreach ($ars as $ar){
                        echo "Adding ".$ar['id']." to this event with the new zip.\n";
                        ZipArtist::create(['artist_id' => $ar['id'],'zip_id' => $dbZipId, 'date' => $date]);
                    }
                    
                }
            }
            echo "Moving on to next event.\n";
        }
    }
    echo "Done with ".$zip.".\n";
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
        $file = fopen("us_postal_codes.csv","r");
        if(DB::table('zips')->count() < 43483){
            while(!feof($file)){  
                $line = (fgetcsv($file));
                if( strlen($line[0]) == 5){
                    if(count(Zip::where( 'zipCode', '=', trim($line[0]) )->get()) == 0){
                        Zip::create(['zipCode' => trim($line[0])]);
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
