<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCpCpConversionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('cp_cp_conversions')){
			Schema::create('cp_cp_conversions', function(Blueprint $table)
			{
				$table->increments('id');
				$table->string('amount', 191);
				$table->string('ref_id', 32)->unique('cp_conversions_ref_id_unique');
				$table->string('from', 10)->index('cp_conversions_from_index');
				$table->string('to', 10)->index('cp_conversions_to_index');
				$table->string('address', 191)->nullable();
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
		Schema::drop('cp_cp_conversions');
	}

}
