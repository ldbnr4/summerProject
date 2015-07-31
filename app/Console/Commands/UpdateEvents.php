<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Event;
use App\Zip;
use App\Artist;

use App\ZipClust;
use DB;
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
                    Zip::create(['zipCode' => $zip, 'clust_id' => 0]);
                }
            }
            fclose($file);
       }
        $zips = DB::table('zips')->get();
        /*var_dump($zips[0]->clust_id);
                return;*/
        Zip::chunk(500, function($zips){
            foreach ($zips as $zip){
                shell_exec('python -c "import pyJamBaseBot; pyJamBaseBot.getEvents(\"'.strval($zip['zipCode']).'\"); "');
                $eString = file_get_contents ('events.txt');
                if(strlen($eString) <= 2 || $eString == 'NULL'){
                    $f = fopen("noEs_zips.txt","a");
                    fwrite($f,$zip['zipCode']." ");
                    fclose($f);
                }
            }
        });
        return;
        
        
        $total = count($zips);
        $x = 0;
        $end_of_db = false;
        $badZips = array();
        while($x < $total){
            $still_looking = true;
            $newClust = false;
            if(is_null($zips[$x]->clusters)){
                shell_exec("python findZipClusts.py ".$zips[$x]->zipCode);
                $f = file_get_contents("zip_clusters.txt");
                while(strlen($f) == 0){
                    array_push($badZips,$zips[$x]->zipCode);
                    $x++;
                    if($x > $total){
                        $end_of_db = true;
                        break;
                    }
                    shell_exec("python findZipClusts.py ".$zips[$x]->zipCode);
                    $f = file_get_contents("zip_clusters.txt");
                }
                echo "New original file. Zip: ".$zips[$x]->zipCode."\n";
                $orgArray = explode (":", $f);
                array_pop($orgArray);
                $y = $x+1;
                while($still_looking == true && $end_of_db == false){
                    while(in_array($zips[$y]->zipCode, $orgArray)){
                        $y++;
                        if($y > $total){
                            $end_of_db = true;
                            break;
                        }
                        //echo "Y is in original array. Incrementing Y: ".$y."\n";
                    }
                    if($end_of_db = true){
                        break;
                    }
                    shell_exec("python findZipClusts2.py ".$zips[$y]->zipCode);
                    $file2 = file_get_contents("zip_clusters2.txt");
                    while(strlen($file2) == 0){
                        array_push($badZips,$zips[$y]->zipCode);
                        $y++;
                        if($y > $total){
                            $end_of_db = true;
                            break;
                        }
                        shell_exec("python findZipClusts2.py ".$zips[$y]->zipCode);
                        $file2 = file_get_contents("zip_clusters.txt");
                    }
                    if($end_of_db = true){
                        break;
                    }
                    echo "New 2nd file. Zip: ".$zips[$y]->zipCode."\n";
                    $file2 = explode (":", $file2);
                    array_pop($file2);
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
                        #$newClust = Clust::create([]);
                        foreach ($orgArray as $l2){
                            $inFileZip2 = trim($l2);
                            #Zip::where('zipCode', '=', $inFileZip2)->update(['clust_id' => $newClust['id']]);
                        }
                        $still_looking = false;
                        $newClust = true;
                    }
                    else{
                        echo "Zip: ".$zips[$y]->zipCode." touches ".$zips[$x]->zipCode."\n";
                        $y++;
                    }
                }
                if( $still_looking == false && $newClust == true){
                    $x = $y;
                }
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
