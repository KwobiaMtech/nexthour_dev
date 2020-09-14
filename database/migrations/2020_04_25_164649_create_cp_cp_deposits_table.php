<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCpCpDepositsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('cp_cp_deposits')){
			Schema::create('cp_cp_deposits', function(Blueprint $table)
			{
				$table->bigInteger('id', true)->unsigned();
				$table->string('address', 128)->index('cp_deposits_address_index');
				$table->string('txn_id', 128)->unique('cp_deposits_txn_id_unique');
				$table->boolean('status');
				$table->string('status_text', 191);
				$table->string('currency', 10);
				$table->boolean('confirms');
				$table->string('amount', 191);
				$table->string('amounti', 191);
				$table->string('fee', 191)->nullable();
				$table->string('feei', 191)->nullable();
				$table->string('dest_tag', 191)->nullable();
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
		Schema::drop('cp_cp_deposits');
	}

}
