<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCpCpCallbackAddressesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('cp_cp_callback_addresses')){
			Schema::create('cp_cp_callback_addresses', function(Blueprint $table)
			{
				$table->increments('id');
				$table->string('address', 128);
				$table->string('currency', 10);
				$table->text('pubkey', 65535)->nullable();
				$table->string('ipn_url', 191)->nullable();
				$table->string('dest_tag', 191)->nullable();
				$table->timestamps();
				$table->unique(['address','currency'], 'cp_callback_addresses_address_currency_unique');
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
		Schema::drop('cp_cp_callback_addresses');
	}

}
