<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCpCpTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('cp_cp_transactions')){
			Schema::create('cp_cp_transactions', function(Blueprint $table)
			{
				$table->bigInteger('id', true)->unsigned();
				$table->string('amount1', 191);
				$table->string('amount2', 191);
				$table->string('currency1', 10);
				$table->string('currency2', 10);
				$table->string('fee', 191)->nullable();
				$table->string('address', 191)->nullable();
				$table->string('dest_tag', 191)->nullable();
				$table->string('buyer_email', 191)->nullable();
				$table->string('buyer_name', 191)->nullable();
				$table->string('item_name', 191)->nullable();
				$table->string('item_number', 191)->nullable();
				$table->string('invoice', 191)->nullable();
				$table->text('custom', 65535)->nullable();
				$table->string('ipn_url', 191)->nullable();
				$table->string('txn_id', 128)->unique('cp_transactions_txn_id_unique');
				$table->boolean('confirms_needed');
				$table->integer('timeout')->unsigned();
				$table->string('status_url', 191);
				$table->string('qrcode_url', 191);
				$table->timestamps();
				$table->smallInteger('status')->nullable();
				$table->string('status_text', 191)->nullable();
				$table->string('received_confirms', 191)->nullable();
				$table->string('received_amount', 191)->nullable();
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
		Schema::drop('cp_cp_transactions');
	}

}
