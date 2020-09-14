<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('custom_pages')){
			Schema::create('custom_pages', function(Blueprint $table)
			{
				$table->integer('id', true);
				$table->string('title', 191);
				$table->string('slug', 191);
				$table->boolean('in_show_menu')->nullable()->default(1);
				$table->string('detail');
				$table->boolean('is_active')->default(1);
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
		Schema::drop('custom_pages');
	}

}
