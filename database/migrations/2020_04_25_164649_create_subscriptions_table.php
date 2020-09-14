<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscriptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('subscriptions')){
			Schema::create('subscriptions', function(Blueprint $table)
			{
				$table->increments('id');
				$table->integer('user_id');
				$table->string('name', 191);
				$table->string('stripe_id', 191);
				$table->string('stripe_plan', 191);
				$table->integer('quantity');
				$table->dateTime('trial_ends_at')->nullable();
				$table->dateTime('ends_at')->nullable();
				$table->dateTime('subscription_from')->nullable();
				$table->dateTime('subscription_to')->nullable();
				$table->integer('amount')->nullable();
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
		Schema::drop('subscriptions');
	}

}
