<html>
	<head>
		<title>Vamos</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	</head>
	<body>
        <div class ="navbar navbar-inverse">
            <?php

                echo "<h1 style='color:white' class ='text-right'> <b style='margin-right: 1%'>Concerts near {$city}, {$stateFull}</b></h1>";
            ?>
        </div>
        <div class="panel panel-primary" style="margin-left: 15%; margin-right: 15%">
            <div class = 'panel-heading text-center'><h3><b>Concerts</b></h3></div> 
            <div class='panel-body'>
                <?php
use App\Artist;
use App\Event;
                    $prevdate = '';
                    $j=0;
foreach($e->where('date', '>=', date('Y-m-d'))->get() as $events){
    $event = unserialize($events['event']);
    $artists = Artist::where('event_id', '=', $events['id'])->get();
    if($prevdate != $event[0]){
            echo "</div>";
        echo '<div class="panel panel-success" style="margin: 1%; border: 2px solid #E5E500">';
        echo "<h4 class='panel-heading'>".date_format(date_create($event[0]), 'D F d, Y')."<br></h4>";
    }
    echo '<div class="panel-body" style = "padding: 0; border:1px solid #E5E500; margin: 1%">';
    
    echo '<div class="panel-heading" style ="float:right;background-color: #E5E500"><h4><strong>'.$events['city'].', '.$events['state'].'</strong></h4></div>';
    
    $firstArt = $artists->first();
    echo '<div class="col-md-4" style = "padding: 5%">';
    echo "<img src = ".$firstArt['pic_url']." alt = 'artist' style = 'max-width:300;max-height:300;padding:5%'>";
    echo "</div>";
    
    echo '<div class="col-md-8 text-center">';
    echo "<h2><strong>".$events['venue']."</strong></h2>";
    echo "<footer><em> PRESENTS: </em></footer><div style='color:#E5E500;height:7px;'>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</div><br>";
    $num = count($artists);
    $i = 1;
    foreach($artists as $artist){
        echo $artist['name'];
        if($i != $num){
            echo ", ";
        }
        $i++;
    }
    echo "</div>";
    echo "</div>";
    echo "<br>";
    //if( $j+1 == (count($events)) || $events[$j][0] != $events[$j+1][0]){
      //  echo "</div>";
    //}
    $prevdate = $event[0];
    //$j++;
    //if($j==10)
        //break;
}

                ?>
            </div>
            </div>
	</body>
</html>
