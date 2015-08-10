<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArtistsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('artists')){
            Schema::create('artists', function(Blueprint $table)
            {
                $table->increments('id');
                $table->string('name')->index();;
                $table->string('pic_url');
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
		Schema::drop('artists');
	}

}
