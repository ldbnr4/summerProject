<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClustsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clusts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
		});
        
        Schema::create('clust_zip', function(Blueprint $table)
		{
			$table->integer('clust_id')->unsigned()->index();
			$table->foreign('clust_id')->references('id')->on('clusts')->onDelete('cascade');
			$table->integer('zip_id')->unsigned()->index();
            $table->foreign('zip_id')->references('id')->on('zips')->onDelete('cascade');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('clust_zip');
        Schema::drop('clusts');
	}

}
