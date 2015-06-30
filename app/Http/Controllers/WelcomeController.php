<?php namespace App\Http\Controllers;

use App\Event;
use App\Zip;

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
        
        $file = fopen("../us_postal_codes.csv","r");
        $i=0;
        while(!feof($file)){
            if($i!=0){
                print_r(fgetcsv($file));
            }
            $i++;
        }
        fclose($file);
       
        /*if(count(Zip::where( 'zipCode', '=', $zip )->get()) == 0){
            Zip::create(['zipCode' => $zip]);
        }*/
        
        /*$events =  getEvents( $zip );
        
        
        foreach ($events as $event) {
            $serial = serialize($event);
            if(count(Event::where( 'event', '=', $serial )->get()) == 0){
                Event::create(['event' => $serial, 'zip' => $zip]);
            } 

        }*/
        
		return 'got your events sir';
	}

}
