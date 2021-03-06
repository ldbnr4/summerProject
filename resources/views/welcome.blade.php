<?php
    $count = ceil(count($e)/15);
?>
<html>
    <style>
        body {
            background-image: url("pics/bg.jpg");
        }
    </style>
	<head>
		<title>Vamos</title>
        <script type='text/javascript' src='https://code.jquery.com/jquery-1.11.3.min.js'></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <script type='text/javascript' src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script type='text/javascript' src="https://esimakin.github.io/twbs-pagination/js/jquery.twbsPagination.js"></script>
        <script type='text/javascript'>//<![CDATA[ 
            var events = Array();
            <?php 
                for($x=1;$x<=$count;$x++){
                    $start = $x*15;
                    $chunk = array_slice($e,$start,15);
                    
                    echo "events.push(".var_dump($chunk).");";
                }
            ?>
            $(window).load(function(){
                $('#pagination-demo').twbsPagination({
                        totalPages: "<?php echo $count ?>",
                        visiblePages: "10",
                        onPageClick: function (event, page) {
                            $('#page-content').text(events[page]);
                        }
                });
            });//]]>  
        </script>
	</head>
	<body>
        <div class ="navbar navbar-inverse">
            <?php

                echo "<h1 style='color:white' class ='text-right'> <b style='margin-right: 1%'>Concerts near {$city}, {$state}</b></h1>";
            ?>
        </div>
        <!--<div id="page-content" class="well">Page 1</div>-->
        <!--<div class="col-md-4">-->
            <!--<ul id="pagination-demo" class="pagination pagination-sm"></ul>-->
       <!-- </div>-->
        <div class="panel panel-primary" style="margin-left: 30%; margin-right: 30%">
            <div class = 'panel-heading text-center'><h3><b>Concerts</b></h3></div> 
            <div class='panel-body'>
                <?php
                    $prevdate = '';
                    $j=0;
                    foreach($e as $event){
                        
                        /****************************
                         *  Date Header and Body    *
                         ****************************/
                        if($prevdate != $event['date']){
                            echo "</div>";
                            echo '<div class="panel panel-success" style="margin: 1%; border: 2px solid #dff0d8">';
                            echo "<h3 class='panel-heading text-center'><strong>".date_format(date_create($event['date']), 'l F d, Y')."</strong></h3>";
                        }
                        
                        /* Event Body Start */
                        echo '<div class="panel-body" style = "padding: 0; border:1px solid #E5E500; margin: 1%;position: relative">';
                        
                        
                       /* Event Location Box */
                        echo '<div class="panel-heading" style ="float:right;background-color: #E5E500"><h4><strong>'.$event['city'].', '.$event['state'].'</strong></h4></div>';
                        $artists = $event['artists'];
                        /* Image Box */
                        $firstArt = $artists[0];
                        echo '<div class="col-md-4" style = "padding: 5%">';
                        echo "<img src = ".$firstArt['pic_url']." alt = 'artist' style = 'max-width:300;max-height:300;padding:5%'>";
                        echo "</div>";
                        
                        /*Event Info Sart*/
                        echo '<div class="col-md-8 text-center" style="">';
                        echo "<h2><strong>".$event['venue']."</strong></h2>";
                        echo "<footer><em> PRESENTS: </em></footer><div style='color:#E5E500;height:7px;'>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</div><br>";
                        echo "<div style = 'padding:3%'>";
                        $num = count($artists);
                        $i = 1;
                        foreach($artists as $artist){
                            echo $artist['name'];
                            if($i != $num){
                                echo ", ";
                            }
                            $i++;
                        }
                        echo '</div>';
                        /*Extra Icons Box*/
                        echo "<div style = 'margin:3%'>";
                        if($event['tic_url'] != "NULL"){
                            echo "<a target='_blank' href = '".$event['tic_url']."'><button class='btn btn-primary'><img src = 'pics/ticket.png' alt = 'ticIcon' style = 'max-width:25;max-height:25;padding:1%;margin-top:5%'><h4>Get Tickets!</h4> </button></a>";
                        }
                        echo "</div>";
                        /* Event Info End */
                        echo "</div>";
                        
                        /* Event Body End */
                        echo "</div>";
                        echo "<br>";
                        //if( $j+1 == (count($events)) || $events[$j][0] != $events[$j+1][0]){
                          //  echo "</div>";
                        //}
                        $prevdate = $event['date'];
                        //$j++;
                        //if($j==10)
                            //break;
                    }

                ?>
            </div>
            </div>
	</body>
</html>
