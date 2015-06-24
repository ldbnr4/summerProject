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
                
                //$details = json_decode(file_get_contents("http://www.telize.com/geoip/204.77.163.50"), true);

                /***************************
                 *    Production Settings  *
                 ***************************/
                $ip = getRealIpAddr();
                $details = json_decode(file_get_contents("http://www.telize.com/geoip/".$ip), true);
                //**************************

                $city = $details['city'];
                $state = $details['region'];
                $zip = $details['postal_code'];

                /***************************
                 *    Production Settings  *
                 ***************************/
                $jamBase=file_get_contents("http://api.jambase.com/events?zipCode=".$zip."&page=0&api_key=zfce2m593mb3zyvu88ksbh49");
                $obj = json_decode($jamBase, true);
                //***************************

                //file_put_contents('JBaseResp.json',$jamBase);
                //$devJBASE = file_get_contents("JBaseResp.json");
                //$obj = json_decode($devJBASE, true);

                echo "<h1 style='color:white' class ='text-right'> <b style='margin-right: 1%'>Concerts near ".$city.", ".$state."</b></h1>";
            ?>
        </div>
        <div class="panel panel-primary" style="margin-left: 15%; margin-right: 15%">
            <div class = 'panel-heading text-center'><h3><b>Concerts</b></h3></div> 
            <div class='panel-body'>
                <?php 
                    $prevdate = '';
                    $prevTime = '';
                    $j=0;
                    foreach($obj['Events'] as $Events){
                        if($prevdate != date_format(date_create($Events['Date']), 'D F d, Y')){
                            echo '<div class="panel panel-success">';
                            echo "<h4 class='panel-heading'>".date_format(date_create($Events['Date']), 'D F d, Y')."<br></h4>";
                        }
                        echo "<div class='panel-body'>";  
                        if($prevTime != date_format(date_create($Events['Date']), 'g:ia')){
                            echo '<div class="panel panel-default">';
                            echo "<h4 class='panel-heading'>".date_format(date_create($Events['Date']), 'g:ia')."<br></h4>";
                            echo "<div class='panel-body'>";
                        }
                        $person = urlencode($Events['Artists'][0]['Name']);
                        //echo $person."<br>";
                        //$pic = file_get_contents("https://api.spotify.com/v1/search?q=".$person."&type=artist");
                        //var_dump($pic);
                        //$pic = json_decode($pic, true);
                        //echo count($pic['artists']['items']);
                        /*if(count($pic['artists']['items']) > 0){
                            if(count($pic['artists']['items'][0]['images']) > 0){
                                if(count($pic['artists']['items'][0]['images'][0]['url']) > 0){
                                    echo '<div class="col-md-4">';
                                    echo "<img src =' ".$pic['artists']['items'][0]['images'][0]['url']." ' alt = 'artist' style = 'max-width:300;max-height:300;'>";
                                    echo "</div>";
                                }
                            }
                        }*/
                        echo '<div class="col-md-8 text-center">';
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
                        echo "<br><a  target='_blank' href =".$Events['TicketUrl'].">Get Tickets</a><br>";
                        echo "</div>";
                        echo "</div>";
                        if( $j+1 == (count($obj['Events'])) || date_format(date_create($obj['Events'][$j]['Date']), 'D F d, Y') != date_format(date_create($obj['Events'][$j+1]['Date']), 'D F d, Y')){
                            echo "</div>";
                        }
                        if( $j+1 == (count($obj['Events'])) || date_format(date_create($obj['Events'][$j]['Date']), 'g:ia') != date_format(date_create($obj['Events'][$j+1]['Date']), 'g:ia')){
                            echo "</div></div>";
                        }else{
                            echo "<hr>";   
                        }
                        
                        $prevdate = date_format(date_create($Events['Date']), 'D F d, Y');
                        $prevTime = date_format(date_create($Events['Date']), 'g:ia');
                        $j++;
                    }
                    
                    //$pic = file_get_contents("https://api.spotify.com/v1/search?q=Tech+N9ne&type=artist");
                    //echo $pic['artists'][0];
                    //var_dump($pic);
                    //$pic = json_decode($pic, true);
        
                    //echo "<img src =' ".$pic['artists']['items'][0]['images'][0]['url']." ' alt = 'artist'>";

                ?>
            </div>
            </div>
        </div>
	</body>
</html>
