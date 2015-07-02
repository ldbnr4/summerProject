<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Event;
use App\Zip;
use App\Artist;

class UpdateEvents extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'Events:update';

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
        set_time_limit ( 100000 );
        
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
        
        include "jamBaseBot.php";
        
        Zip::chunk(500, function($zips){
            foreach($zips as $zip){
                $events = getEvents($zip['zipCode']);
                foreach( $events as $event ){
                    $location = explode(',', $event[3]);
                    $city = trim($location[0]);
                    $state = trim($location[1]);
                    if(count(Event::where( 'event', '=', serialize($event) )->get()) == 0){
                        $newE = Event::create(['event' => trim(serialize($event)), 
                                               'zip' => trim($zip['zipCode']),
                                               'date' => trim($event[0]),
                                               'venue' => trim($event[2]),
                                               'city' => $city,
                                               'state' => $state
                                              ]);
                        foreach($event[1] as $artist){
                            if(count(Artist::where( 'name', '=', $artist )->get()) == 0){
                                Artist::create(['name' => trim($artist), 'event_id' => $newE['id']]);
                            }
                        }
                    }
                    
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