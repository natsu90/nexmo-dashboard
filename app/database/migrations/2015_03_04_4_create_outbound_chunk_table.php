<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutboundChunkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('outbound_chunk', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('outbound_id')->unsigned();
			$table->foreign('outbound_id')
      			->references('id')->on('outbound')
      			->onDelete('cascade');
			$table->string('message_id', 30);
			$table->tinyInteger('status_code')->unsigned();
			$table->double('price', 8, 8);

			$table->string('dn_status', 30)->default('pending');
			$table->tinyInteger('dn_error_code')->default('-1');
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
		Schema::drop('outbound_chunk');
	}

}
