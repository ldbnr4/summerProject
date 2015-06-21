<html>
	<head>
		<title>Vamos</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	</head>
	<body>
        <div class ="navbar navbar-inverse">
            <?php
                function getRealIpAddr()
                {
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
                
                //$details = json_decode(file_get_contents("http://ipinfo.io/204.77.163.50/json"));
                //$details = json_decode(file_get_contents("http://ipinfo.io/64.136.220.166/json"));
                
                /***************************
                 *    Production Settings  *
                 ***************************/
                $ip = $ip=$_SERVER['HTTP_CLIENT_IP'];
                echo $ip;
                $details = json_decode(file_get_contents("http://ipinfo.io/".$ip."/json"));
                var_dump($details);
                //**************************

                //$city = $details->city;
                //$state = $details->region;
                //$zip = $details->postal;

                /***************************
                 *    Production Settings  *
                 ***************************/
                //$jamBase=file_get_contents("http://api.jambase.com/events?zipCode=".$zip."&page=0&api_key=zfce2m593mb3zyvu88ksbh49");
                //$obj = json_decode($jamBase, true);
                //***************************
                
                //file_put_contents('JBaseResp.json',$jamBase);
                $devJBASE = file_get_contents("JBaseResp.json");
                $obj = json_decode($devJBASE, true);

                //echo "<h1 style='color:white' class ='text-right'> <b style='margin-right: 1%'>Concerts near ".$city.", ".$state."</b></h1>";
            ?>
        </div>
        <div class="panel panel-primary" style="margin-left: 15%; margin-right: 15%">
            <div class = 'panel-heading text-center'><h3><b>Concerts</b></h3></div>
            <div class="panel-body">   
                <?php 

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

                        echo "<hr>";
                    }
                    
                    $pic = file_get_contents("https://api.spotify.com/v1/search?q=Sigma&type=artist");
                    //echo $pic['artists'][0];
                    //var_dump($pic);
                    $pic = json_decode($pic, true);
        
                    var_dump( $pic['artists']['items']);

                ?>

            </div>
        </div>
	</body>
</html>
