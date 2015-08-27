@extends('app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Home</div>

				<div class="panel-body">
					Num of artists = {{$ARTNUM}}
                    <br>
                    Last 10 entered into the db:
                    <br>
                    <?php 
                        foreach($LAST10 as $new){
                            var_dump($new);
                            echo '<br>';
                        }
                        //var_dump($LAST10);
                    ?>
                    <br>
                    Duplicates =
                    <br>
                    <?php 
                        foreach($DUPS as $dup){
                            var_dump($dup);
                            echo '<br>';
                        }
                        //var_dump($LAST10);
                    ?>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
