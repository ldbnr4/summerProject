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
        
/*        $file = fopen("us_postal_codes.csv","r");
        while(!feof($file)){  
            $line = (fgetcsv($file));
            if( strlen($line[0]) == 5){
                if(count(Zip::where( 'zipCode', '=', trim($line[0]) )->get()) == 0){
                    Zip::create(['zipCode' => trim($line[0])]);
                }
            }
        }
        fclose($file);*/
       /* Zip::chunk(500, function($zips){
            foreach($zips as $zip){
                $events = getEvents($zip['zipCode']);
                
                foreach( $events as $event ){
                    $location = explode(',', $event[3]);
                    $city = trim($location[0]);
                    $state = trim($location[1]);
                    $tic_url = $event[4];
                    if(count(Event::where( 'event', '=', serialize($event) )->get()) == 0){
                        $newE = Event::create(['event' => trim(serialize($event)), 
                                               'zip' => trim($zip['zipCode']),
                                               'date' => trim($event[0]),
                                               'venue' => trim($event[2]),
                                               'city' => $city,
                                               'state' => $state,
                                               'tic_url' => $tic_url
                                              ]);
                        foreach($event[1] as $artist){
                            $artist = trim($artist);
                            $pic_url = shell_exec('python -c "import pyPicBot; pyPicBot.getPic(\"'.$artist.'\"); "');
                            Artist::create(['name' => $artist, 'event_id' => $newE['id'], 'pic_url' => $pic_url]);
                        }
                    }    
                }    
            }
        });*/
        
        $zip = 64138;
        
        $dbZip = Zip::where( 'zipCode', '=', $zip);
        if(count($dbZip) == 0){
            $dbZip = Zip::create(['zipCode' => trim($zip)]);
        }
        $dbZipId = $dbZip->id;
        echo 'here';
        
        $dbZE = ZipEvent::where( 'zip_id', '=', $dbZipId);
        if($dbZE->count() == 0){
            //call pyJameBaseBot
        }
        $dbZEs = $dbZE->get();
        $dates = shell_exec('python pyDates.py');
        $dates = explode(':', $dates);
        $today = new DateTime($dates[0]);
        $later = new DateTime($dates[1]);
        foreach($dbZEs as $ZE){
            $qEid = $ZE['event_id'];
            $eve = Event::find($qEid);
            $eveDate = new DateTime($eve['date']);
            
        }
        
        
        $eString = shell_exec('python -c "import pyJamBaseBot; pyJamBaseBot.getEvents(\"'.$zip.'\"); "');
        if($eString != 'NULL'){
            $eArray = explode('|', $eString);
            $eArray = str_replace('[', '',$eArray);
            $eArray = str_replace(']', '',$eArray);
            foreach ($eArray as $eventA){
                $event = explode(';', $eventA);
                if( count($event) == 5){
                    $event = str_replace(', ', '', $event);
                    $event = str_replace("'", '', $event);
                    $artist = explode(':', $event[1]);
                    array_pop($artist);
                    
                    
                    
                    
                    if(count($artist) >= 1){
                        
                    }
                    var_dump ($event);
                    //break;
                }
            }
        }
        
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
