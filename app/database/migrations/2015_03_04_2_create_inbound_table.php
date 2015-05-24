<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInboundTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inbound', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('from', 30);
			$table->bigInteger('to')->unsigned();
			$table->text('text');
			$table->string('type', 30);
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
		Schema::drop('inbound');
	}

}
