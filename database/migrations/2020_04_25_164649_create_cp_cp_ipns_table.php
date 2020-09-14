<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCpCpIpnsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('cp_cp_ipns')){
			Schema::create('cp_cp_ipns', function(Blueprint $table)
			{
				$table->bigInteger('id', true)->unsigned();
				$table->string('ipn_version', 10);
				$table->string('ipn_id', 128)->unique('cp_ipns_ipn_id_unique');
				$table->string('ref_id', 191)->nullable();
				$table->string('ipn_mode', 32);
				$table->string('merchant', 191);
				$table->string('ipn_type', 32)->nullable();
				$table->string('address', 128)->nullable()->index('cp_ipns_address_index');
				$table->string('txn_id', 191)->nullable();
				$table->smallInteger('status')->nullable();
				$table->string('status_text', 191)->nullable();
				$table->string('currency', 191)->nullable();
				$table->string('currency1', 191)->nullable();
				$table->string('currency2', 191)->nullable();
				$table->boolean('confirms')->nullable();
				$table->string('amount', 191)->nullable();
				$table->string('amounti', 191)->nullable();
				$table->string('amount1', 191)->nullable();
				$table->string('amount2', 191)->nullable();
				$table->string('fee', 191)->nullable();
				$table->string('feei', 191)->nullable();
				$table->string('dest_tag', 191)->nullable();
				$table->string('buyer_name', 191)->nullable();
				$table->string('item_name', 191)->nullable();
				$table->string('item_number', 191)->nullable();
				$table->string('invoice', 191)->nullable();
				$table->text('custom', 65535)->nullable();
				$table->string('send_tx', 191)->nullable();
				$table->string('received_amount', 191)->nullable();
				$table->string('received_confirms', 191)->nullable();
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
		Schema::drop('cp_cp_ipns');
	}

}
