<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZipArtistsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('zip_artists')){
            Schema::create('zip_artists', function(Blueprint $table)
            {
                $table->increments('id');
                $table->integer('zip_id')->unsigned();
                $table->integer('artist_id')->unsigned();
                $table->date('date');
                $table->foreign('zip_id')->references('id')->on('zips')->onDelete('cascade');
                $table->foreign('artist_id')->references('id')->on('artists')->onDelete('cascade');
                $table->timestamps();
            });
        }
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('zip_artists');
	}

}
