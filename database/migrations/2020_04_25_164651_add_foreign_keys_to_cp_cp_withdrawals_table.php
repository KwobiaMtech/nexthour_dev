<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCpCpWithdrawalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('cp_cp_withdrawals')){
			Schema::table('cp_cp_withdrawals', function(Blueprint $table)
			{
				$table->foreign('mass_withdrawal_id', 'cp_withdrawals_mass_withdrawal_id_foreign')->references('id')->on('cp_cp_mass_withdrawals')->onUpdate('CASCADE')->onDelete('RESTRICT');
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
		Schema::table('cp_cp_withdrawals', function(Blueprint $table)
		{
			$table->dropForeign('cp_withdrawals_mass_withdrawal_id_foreign');
		});
	}

}
