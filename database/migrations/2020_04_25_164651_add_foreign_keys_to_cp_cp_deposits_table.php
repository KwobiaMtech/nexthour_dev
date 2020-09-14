<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCpCpDepositsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('cp_cp_deposits')){
			Schema::table('cp_cp_deposits', function(Blueprint $table)
			{
				$table->foreign('address', 'cp_deposits_address_foreign')->references('address')->on('cp_cp_callback_addresses')->onUpdate('CASCADE')->onDelete('RESTRICT');
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
		Schema::table('cp_cp_deposits', function(Blueprint $table)
		{
			$table->dropForeign('cp_deposits_address_foreign');
		});
	}

}
