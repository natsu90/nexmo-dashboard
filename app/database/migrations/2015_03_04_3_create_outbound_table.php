<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutboundTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('outbound', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('from', 30);
			$table->bigInteger('to')->unsigned();
			$table->string('status', 30)->default('queued');
			$table->text('text');
			$table->string('type', 30)->default('text');
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
		Schema::drop('outbound');
	}

}
