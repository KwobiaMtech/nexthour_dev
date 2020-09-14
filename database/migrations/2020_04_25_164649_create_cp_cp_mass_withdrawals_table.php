<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCpCpMassWithdrawalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('cp_cp_mass_withdrawals')){
			Schema::create('cp_cp_mass_withdrawals', function(Blueprint $table)
			{
				$table->increments('id');
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
		Schema::drop('cp_cp_mass_withdrawals');
	}

}
