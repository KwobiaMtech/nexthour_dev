<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('notifications')){
			Schema::create('notifications', function(Blueprint $table)
			{
				$table->char('id', 36)->primary();
				$table->string('type', 191);
				$table->string('notifiable_id', 200);
				$table->string('notifiable_type', 191);
				$table->string('title', 200)->nullable();
				$table->text('data', 65535);
				$table->integer('movie_id')->nullable();
				$table->integer('tv_id')->nullable();
				$table->dateTime('read_at')->nullable();
				$table->timestamps();
				$table->index(['notifiable_id','notifiable_type']);
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
		Schema::drop('notifications');
	}

}
