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
        set_time_limit ( 1000000 );
        include "../jamBaseBot.php";
        include "../ipBot.php";
        include "../picBot.php";
        
        function getRealIpAddr(){
            if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
            {
              $ip=$_SERVER['HTTP_CLIENT_IP'];
            }
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
            {
              $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else
            {
              $ip=$_SERVER['REMOTE_ADDR'];
            }
            return $ip;
        }
        
        //$location = getLocation(getRealIpAddr());
        $location = getLocation('204.77.163.50');
        $city = trim($location[2]);
        $state = trim($location[0]);
        $zip = trim($location[3]);
        $stateFull = trim($location[1]);
        
        $zcheck = Zip::where( 'zipCode', '=', $zip)->get();
        
        if(count($zcheck) == 0){
            Zip::create(['zipCode' => trim($zip)]);
        }
        
        $echeck = Event::where( 'zip', '=', $zip );
        
        if(($echeck->count()) == 0){
            $events = getEvents($zip);
            foreach( $events as $event ){
                    $location = explode(',', $event[3]);
                    $city2 = trim($location[0]);
                    $state = trim($location[1]);
                    $tic_url = $event[4];
                    if(count(Event::where( 'event', '=', serialize($event) )->get()) == 0){
                        $newE = Event::create(['event' => trim(serialize($event)), 
                                               'zip' => trim($zip),
                                               'date' => trim($event[0]),
                                               'venue' => trim($event[2]),
                                               'city' => $city2,
                                               'state' => $state,
                                               'tic_url' => $tic_url
                                              ]);
                        foreach($event[1] as $artist){
                            $pic_url = getPic(trim($artist));
                            Artist::create(['name' => trim($artist), 'event_id' => $newE['id'], 'pic_url' => $pic_url]);
                        }
                    }
                    
                }
            $e = $newE;
        }else{
            $e = $echeck;
        }
        return view('welcome', compact('e', 'city', 'stateFull'));
        
        
	}

}
