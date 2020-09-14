<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCpCpWithdrawalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('cp_cp_withdrawals')){
			Schema::create('cp_cp_withdrawals', function(Blueprint $table)
			{
				$table->bigInteger('id', true)->unsigned();
				$table->integer('mass_withdrawal_id')->unsigned()->nullable()->index('cp_withdrawals_mass_withdrawal_id_index');
				$table->string('amount', 191);
				$table->string('amount2', 191)->nullable();
				$table->string('amounti', 191)->nullable();
				$table->string('currency', 10);
				$table->string('currency2', 10)->nullable();
				$table->string('address', 191)->nullable();
				$table->string('pbntag', 191)->nullable();
				$table->string('dest_tag', 191)->nullable();
				$table->string('ipn_url', 191)->nullable();
				$table->boolean('auto_confirm')->default(0);
				$table->text('note', 65535)->nullable();
				$table->string('ref_id', 128)->unique('cp_withdrawals_ref_id_unique');
				$table->smallInteger('status');
				$table->string('status_text', 191)->nullable();
				$table->timestamps();
				$table->string('txn_id', 128)->nullable()->unique('cp_withdrawals_txn_id_unique');
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
		Schema::drop('cp_cp_withdrawals');
	}

}
