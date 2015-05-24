<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('call_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('call_id', 40);
			$table->bigInteger('to')->unsigned();
			$table->string('status', 30);
			$table->float('price');
			$table->float('rate');
			$table->mediumInteger('duration')->unsigned();
			$table->dateTime('start_time');
			$table->dateTime('end_time');
			$table->char('direction', 3);
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
		Schema::drop('call_logs');
	}

}
