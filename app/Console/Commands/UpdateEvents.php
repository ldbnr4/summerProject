<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Event;
use App\Zip;
use App\Artist;
use DB;
use App\Clust;
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
        
        set_time_limit ( 1000000 );
        
        if(count(DB::table('zips')->get()) == 0 ){
           $file = fopen("cities.csv","r");
            while(!feof($file)){  
                $line = (fgetcsv($file));
                if(is_numeric(trim($line[0]))){
                    $zip = trim($line[0]);
                    while(strlen($zip) != 5){
                        $zip = strval($zip);
                        $zip = '0'.$zip;

                    }
                    $zip = trim($zip);
                    $noEs = file_get_contents('noEs_zips.txt');
                    $noEs = explode (" ", $noEs);
                    array_pop($noEs);
                    if(!(in_array($zip, $noEs))){
                        Zip::create(['zipCode' => $zip]);
                    }
                }
            }
            fclose($file);
        }
        /*$zips = DB::table('zips')->get();
        var_dump($zips[0]->clust_id);
                return;*/
        /*Zip::chunk(500, function($zips){
            foreach ($zips as $zip){
                shell_exec('python -c "import pyJamBaseBot; pyJamBaseBot.getEvents(\"'.strval($zip['zipCode']).'\"); "');
                $eString = file_get_contents ('events.txt');
                if(strlen($eString) <= 2 || $eString == 'NULL'){
                    $f = fopen("noEs_zips.txt","a");
                    fwrite($f,$zip['zipCode']." ");
                    fclose($f);
                }
                
            }
        });*/
       /* Zip::chunk(500, function($zips){
            foreach ($zips as $zip){
                echo $zip['zipCode']."\n";
                shell_exec('python noGoodZips.py '.strval($zip['zipCode']));              
            }
        });
        return;*/
        
        $zips = DB::table('zips')->orderBy('zipCode', 'desc')->get();
        $total = count($zips);
        $x = 0;
        $end_of_db = false;
        $badZips = array();
        $noEsArray = file_get_contents('noEs_zips.txt');
        $noEsArray = explode (" ", $noEsArray);
        array_pop($noEsArray);
        while($x < $total){
            $still_looking = true;
            $newClust = false;
            if(is_null($zips[$x]->clusters)){
            
                echo shell_exec("python findZipClusts.py ".$zips[$x]->zipCode);
                $f = file_get_contents("zip_clusters.txt");
                while(strlen($f) == 0){
                    array_push($badZips,$zips[$x]->zipCode);
                    $x++;
                    if($x >= $total){
                        $end_of_db = true;
                        break;
                    }
                    echo shell_exec("python findZipClusts.py ".$zips[$x]->zipCode);
                    $f = file_get_contents("zip_clusters.txt");
                }
                if($end_of_db == false){
                    echo "New original file. Zip: ".$zips[$x]->zipCode."\n";
                    $orgArray = explode (":", $f);
                    array_pop($orgArray);
                    $orgArray = array_diff($orgArray, $noEsArray);
                    $y = $x + 1;
                    //echo "X: ".$x." Y: ".$y."\n";
                }
                while($still_looking == true && $end_of_db == false){
                    while(in_array($zips[$y]->zipCode, $orgArray)){
                        //echo "incrementing y";
                        $y++;
                        if($y >= $total){
                            $end_of_db = true;
                            break;
                        }
                    }
                    if($end_of_db == true){
                        break;
                    }
                    echo shell_exec("python findZipClusts2.py ".$zips[$y]->zipCode);
                    $file2 = file_get_contents("zip_clusters2.txt");
                    while(strlen($file2) == 0){
                        array_push($badZips,$zips[$y]->zipCode);
                        $y++;
                        if($y >= $total){
                            $end_of_db = true;
                            break;
                        }
                        echo shell_exec("python findZipClusts2.py ".$zips[$y]->zipCode);
                        $file2 = file_get_contents("zip_clusters.txt");
                    }
                    if($end_of_db == true){
                        break;
                    }
                    //echo "New 2nd file. Zip: ".$zips[$y]->zipCode."\n";
                    echo "X: ".$x." Y: ".$y."\n";
                    $file2 = explode (":", $file2);
                    array_pop($file2);
                    $file2 = array_diff($file2, $noEsArray);
                    $i = 0;
                    foreach($file2 as $FL2){
                        $zipF = trim($FL2);
                        if(in_array($zipF, $orgArray)){
                            $i++;
                            break;
                        }
                    }
                    if($i==0){
                        echo "NEW CLUST FOUND! Zip: ".$zips[$y]->zipCode." Does not touch ".$zips[$x]->zipCode."\n";
                        $newClust = Clust::create([]);
                        foreach ($orgArray as $l2){
                            $inFileZip2 = trim($l2);
                            $newnew = Zip::where('zipCode', '=', $inFileZip2)->get();
                            //var_dump($newnew);
                            //var_dump($newnew[0]);
                            if(!(is_null($newnew[0]))){
                                $newnew[0]->clusters()->attach($newClust['id']);
                            }
                            //return;
                            
                        }
                        $still_looking = false;
                        $newClust = true;
                    }
                    else{
                        //echo "Zip: ".$zips[$y]->zipCode." touches ".$zips[$x]->zipCode."\n";
                        $y++;
                        if($y >= $total){
                            $end_of_db = true;
                        }
                    }
                }
                if( $still_looking == false && $newClust == true ){
                    $x = $y;
                }
            }
            else{
                $x++;
            }
        }
        file_put_contents("bad_zip_codes.txt", print_r($badZips, true));
        var_dump($badZips);
        
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
