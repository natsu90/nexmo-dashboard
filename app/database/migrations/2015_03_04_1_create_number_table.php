<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNumberTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('number', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('number')->unsigned();
			$table->string('country_code', 2);
			$table->string('type', 30);
			$table->string('features', 100);
			$table->string('voice_callback_type', 10);
			$table->string('voice_callback_value');
			$table->string('own_callback_url');
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
		Schema::drop('number');
	}

}
