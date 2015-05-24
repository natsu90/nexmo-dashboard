<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWaContactTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_contact', function(Blueprint $table)
		{
			$table->integer('number')->unsigned();
			$table->timestamp('last_seen')->nullable();
			$table->tinyInteger('status')->default(0);
			$table->bigInteger('number_id')->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_contact');
	}

}
