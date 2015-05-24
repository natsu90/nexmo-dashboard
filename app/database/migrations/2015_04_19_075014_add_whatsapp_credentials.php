<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWhatsappCredentials extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('number', function(Blueprint $table)
		{
			$table->string('wa_password')->nullable();
			$table->string('wa_identity')->nullable();
			$table->timestamp('wa_expiration')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('number', function(Blueprint $table)
		{
			$table->dropColumn('wa_password');
			$table->dropColumn('wa_identity');
			$table->dropColumn('wa_expiration');
		});
	}

}
