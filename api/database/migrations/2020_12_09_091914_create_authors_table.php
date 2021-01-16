<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('authors', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('name');
			$table->string('email');
			$table->string('streetAddress')->nullable();
			$table->string('city')->nullable();
			$table->string('phoneNumber')->nullable();
			$table->string('company')->nullable();
			$table->string('catchPhrase')->nullable();
			$table->string('freeText')->nullable();
			$table->date('dt')->nullable();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authors');
    }
}
