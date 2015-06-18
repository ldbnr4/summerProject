<html>
	<head>
		<title>Laravel</title>
	</head>
	<body>
        <div>   
            <?php 
  
   //$details = file_get_contents("http://ipinfo.io/".$_SERVER['REMOTE_ADDR']."/json");
$details = json_decode(file_get_contents("http://ipinfo.io/204.77.163.50/json"));
$city = $details->city;
$state = $details->region;
   
    $zip = $details->postal;
//$jamBase=file_get_contents("http://api.jambase.com/events?zipCode=".$zip."&page=0&api_key=zfce2m593mb3zyvu88ksbh49");

//file_put_contents('JBaseResp.json',$jamBase);
//    $obj = json_decode($jamBase, true);
    $devJBASE = file_get_contents("JBaseResp.json");
    $obj = json_decode($devJBASE, true);
echo "<h1> Concerts near ".$city.", ".$state."</h1>";
foreach($obj['Events'] as $Events){
    echo "Date: ".date_format(date_create($Events['Date']), 'D F d, Y g:ia T')."<br>";
    echo "Venue: <a  target='_blank' href=".$Events['Venue']['Url'].">".$Events['Venue']['Name']."</a> | ".$Events['Venue']['City'].", ".$Events['Venue']['State'];
    echo "<br>Artist: ";
    $num = count($Events['Artists']);
    $i = 1;
    foreach($Events['Artists'] as $Artists){
       echo $Artists['Name'];
        if($i!=$num){
            echo ", ";
        }
        $i++;
    }
    echo "<br><a  target='_blank' href =".$Events['TicketUrl'].">Get Tickets</a>";
    
    echo "<hr><br>";
}
/*for($i = 0;$i<count($obj['Events']);$i++){
    echo '<b>'.$obj['Events'][$i]['Venue']['Name'].'</b><br>';
    for($j = 0; $j < count($obj['Events'][$i]['Artists']);$j++){
        echo $obj['Events'][$i]['Artists'][$j]['Name'].' / ';
    }
    echo'<br><br>';
}*/
//var_dump($obj['Events'][1]['Venue']['Name']);
            ?>
                        
        </div>
	</body>
</html>
