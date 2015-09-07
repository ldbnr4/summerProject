<?php namespace App\Http\Controllers;

use App\Event;
use App\Zip;
use App\Artist;
use DB;

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
        
        function getCompleteEs($zipC){  
            $completeEsArray = array();
            echo shell_exec('bash ../getEsPY.sh '.$zipC);
            $eString = file_get_contents ('../ENV/bin/events.txt');
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
                        $allArtArray = array();
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
                        }
                        if(count($artist) >= 1){
                            foreach($artist as $art){
                                $artArray = array();
                                $art = trim($art);
                                if($art == ''){
                                    $art = "Unkown";
                                }
                                $artArray['name'] = $art;
                                $newArt = Artist::where( 'name', '=', $art);
                                if($newArt->count() == 0){
                                    shell_exec('bash ../getPicPY.sh '.urlencode($art));
                                    $pic_url = file_get_contents('../ENV/bin/pic.txt');
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
                                array_push($allArtArray, $artArray);
                            }
                        }
                        $indivE['artists'] = $allArtArray;
                    }
                    array_push($completeEsArray,$indivE);
                }
            }
            else{
                $f = fopen("bad_zips.txt","a");
                fwrite($f,$zipC." ");
                fclose($f);
            }
            return $completeEsArray;
        }
        
        //$location = shell_exec('bash ../getIPPY.sh 204.77.163.50');
        $location = shell_exec('bash ../getIPPY.sh '.getRealIpAddr());
        $location = explode("\n",$location);
        $city = trim($location[1]);
        $state = trim($location[0]);
        $zip = trim($location[2]);

        $e = getCompleteEs($zip);
        
        
        return view('welcome', compact('e', 'city', 'state'));
        
        
	}
    
    public function update()	{
        set_time_limit ( 1000000 );
        function JB($zip){
            echo shell_exec('bash ../getEsPY.sh '.$zip);
            $eString = file_get_contents ('../ENV/bin/events.txt');
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
                    if( count($event) == 6 ){
                        $artist = explode(':', $event[2]);
                        if(count($artist) >= 1){
                            foreach($artist as $art){
                                $art = trim($art);
                                if($art == ''){
                                    $art = "Unkown";
                                }
                                $newArt = Artist::where( 'name', '=', $art);
                                if($newArt->count() == 0){
                                    shell_exec('bash ../getPicPY.sh '.urlencode($art));
                                    $pic_url = file_get_contents('../ENV/bin/pic.txt');
                                    if(is_null($pic_url) || $pic_url == ''){
                                        $pic_url = 'pics/concert.jpg';
                                    }
                                    $pic_url = trim($pic_url);
                                    $newArt = Artist::create([  'name' => $art, 'pic_url' => $pic_url]);
                                    $newArtId = $newArt['id'];
                                }
                            }
                        }
                    }
                }
            }
            else{
                $f = fopen("bad_zips.txt","a");
                fwrite($f,$zip." ");
                fclose($f);
            }
        }
        
        $zips = (DB::table('zips')->get());
        foreach($zips as $zip){
            JB($zip->zipCode);
        }
        
        return(DB::table('artists')->count());
    }
    
    public function test(){
        
        $ARTNUM = DB::table('artists')->count();
        $LAST10 = DB::table('artists')->orderBy('updated_at','desc')->take(10)->get();
        $DUPS = DB::table('artists')->select(DB::raw('name', 'count(*)'))->groupBy('name')->having(DB::raw('count(*)'),'>',1)->get();
     
        return view('stats', compact('ARTNUM','LAST10','DUPS'));
    }

}
