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
        include "../ipBot.php";
        
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
        //$zip = 00501;
        $stateFull = trim($location[1]);
        
        $echeck = Event::where( 'zip', '=', $zip );
        
        
        return view('welcome', compact('e', 'city', 'stateFull'));
        
        
	}

}
