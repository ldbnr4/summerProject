<?php namespace App\Http\Controllers;

use App\Event;
use App\Zip;
use App\Artist;

class WelcomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('welcome');
	}
    
    public function getEvents()
	{
        include "../jamBaseBot.php";
        
        set_time_limit ( 3000 );
        /*$events =  getEvents( '66062' );
        foreach($events as $event){
            if(count(Event::where( 'event', '=', serialize($event) )->get()) == 0){
                var_dump($event);
            }
        }*/
        
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
        
        /*$events =  getEvents( $zip );
        
        foreach ($events as $event) {
            $serial = serialize($event);
            if(count(Event::where( 'event', '=', $serial )->get()) == 0){
                Event::create(['event' => $serial, 'zip' => $zip]);
            } 

        }*/
        
		return 'got your events sir';
	}
	public function getZips()
	{
        set_time_limit ( 100000 );
        
        $file = fopen("../us_postal_codes.csv","r");
        $i=0;
        while(!feof($file)){  
            $line = (fgetcsv($file));
            if( strlen($line[0]) == 5){
                if(count(Zip::where( 'zipCode', '=', $line[0] )->get()) == 0){
                    Zip::create(['zipCode' => trim($line[0])]);
                }
            }
        }
        fclose($file);
		return 'got zips';
	}

}
