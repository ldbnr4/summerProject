<?php
    include 'picBot.php';
    $i = 0.00;
    for($x = 0; $x<100; $x++){
        $start = microtime('get_as_float');
        getPic( 'Ludacris' );
        $end = microtime('get_as_float');
        $i+=($end-$start);
        }
    echo getPic( 'Ludacris' );
    echo"\n";
    echo $i/100;
?>