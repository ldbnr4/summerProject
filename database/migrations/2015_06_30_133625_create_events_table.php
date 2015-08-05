<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('events')){
            Schema::create('events', function(Blueprint $table)
            {
                $table->increments('id');
                $table->text('event');
                $table->date('date');
                $table->string('venue');
                $table->string('city');
                $table->string('state');
                $table->text('tic_url');
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
		Schema::drop('events');
	}

}
