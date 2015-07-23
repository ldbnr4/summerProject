<?php namespace App\Console\Commands;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Event;
use App\Zip;
use App\Artist;
use App\ZipArtist;
use App\ZipEvent;
use App\EventArtist;
use DB;

function JB ($zip, $dbZipId){
    $eString = shell_exec('python -c "import pyJamBaseBot; pyJamBaseBot.getEvents(\"'.$zip.'\"); "');
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
            $event = str_replace("None", '', $event);
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
                                $pic_url = shell_exec('python -c "import pyPicBot; pyPicBot.getPic(\"'.urlencode($art).'\"); "');
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
        
        chdir('ENV/bin');
        shell_exec('source activate');
        set_time_limit ( 1000000 );
        $Zcheck = Zip::all()->count();
        if($Zcheck == 0){
            $file = fopen("us_postal_codes.csv","r");
            while(!feof($file)){  
                $line = (fgetcsv($file));
                if( strlen($line[0]) == 5){
                    if(count(Zip::where( 'zipCode', '=', trim($line[0]) )->get()) == 0){
                        Zip::create(['zipCode' => trim($line[0])]);
                    }      
                }
            }
            fclose($file);   
        }
        Zip::chunk(500, function($zips){
            foreach($zips as $zip){
                $dbZipId = $zip['id'];
                $ZEcheck = DB::table('zip_events')->select('date')->where('zip_id', '=', $dbZipId)->orderBy('date', 'desc')->first();
                
                JB($zip['zipCode'], $dbZipId);
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
