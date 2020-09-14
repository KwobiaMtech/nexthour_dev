<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCpCpTransfersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('cp_cp_transfers')){
			Schema::create('cp_cp_transfers', function(Blueprint $table)
			{
				$table->bigInteger('id', true)->unsigned();
				$table->string('amount', 191);
				$table->string('currency', 10);
				$table->string('merchant', 191)->nullable();
				$table->string('pbntag', 191)->nullable();
				$table->boolean('auto_confirm')->default(0);
				$table->string('ref_id', 128)->unique('cp_transfers_ref_id_unique');
				$table->smallInteger('status')->index('cp_transfers_status_index');
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
		Schema::drop('cp_cp_transfers');
	}

}
