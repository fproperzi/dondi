<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDondiTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('impianti', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('codice')->unique()->index();
			$table->string("impianto");
			$table->string("tipo");
			$table->string("cat")->nullable();
			$table->string("lotto")->nullable();
			$table->string("comune")->nullable();
			$table->string("ae")->nullable();
			$table->string("limiti")->nullable();
			$table->string("stato")->nullable();
			$table->string("num")->nullable();
			$table->string("data")->nullable();
			$table->string("autotizzazione")->nullable();
			$table->string("accessibilita")->nullable();
			$table->string("superficie")->nullable();
			$table->string("nord")->nullable();
			$table->string("est")->nullable();
			$table->string("latitude")->nullable();
			$table->string("longitude")->nullable();
			$table->string("tecnico_responsabile")->nullable();
			$table->string("squadra")->nullable();
			$table->string("responsabile")->nullable();
		});
		Schema::create('cat_impianti', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('cat')->unique();
			$table->string('categoria')->unique();
		}); 
		Schema::create('tipi_impianto', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('tipo')->unique();
		});
		Schema::create('codici_cer', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('cdcer')->unique();
			$table->string('dscer')->nullable();
			$table->unsignedTinyInteger('bconferimento')->default(0);
		});
		
		Schema::create('lookup', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('tag')->index();  // cer, conferimenti,azconf:aziendaconferimeti, 
			//$table->unsignedBigInteger('tag_id')->nullable();
			$table->string('tag_info_1');
			$table->string('tag_info_2')->nullable();
			$table->string('tag_info_3')->nullable();
			$table->string('tag_info_4')->nullable();

		});
		Schema::create('macchine', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('targa')->unique();  
			$table->string('tipo'); 
			$table->string('foto')->nullable();

		});
		Schema::create('macchine_carico', function (Blueprint $table) {
			$table->id();		// record aperto finche non viene salvato uno scarico
			$table->timestamps();
			$table->dateTime('carico_at')->nullable();
			$table->dateTime('scarico_at')->nullable();
			$table->float('carico_km')->unsigned()->nullable();	// quanti km a inizio gita... facoltativo
			$table->float('scarico_km')->unsigned();			// quanti km a fine gita
			$table->float('euro')->unsigned()->nullable();
			$table->float('litri')->unsigned()->nullable();
			$table->unsignedBigInteger('user_id');
			$table->unsignedBigInteger('macchina_id');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
			$table->foreign('macchina_id')->references('id')->on('macchine')->onDelete('SET NULL');
		});
		Schema::create('localita', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('localita')->unique();
			$table->string('comune');
			$table->string('cap')->nullable();
		});


		Schema::create('foto')->nullable(); function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->unsignedBigInteger('user_id');
			$table->string('foto');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
		});
		
		Schema::create('impianti_foto', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->unsignedBigInteger('impianto_id');
			$table->unsignedBigInteger('foto_id');
			$table->foreign('impianto_id')->references('id')->on('impianti')->onDelete('SET NULL');
			$table->foreign('foto_id')->references('id')->on('foto')->onDelete('SET NULL');

		});

		Schema::create('tipo_attivita', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('tipo')->unique();  // ordinaria,straordinaria
		});
		Schema::create('attivita', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('attivita');
			$table->unsignedBigInteger('tipo_impianto_id')->nullable();  //depuratore,imhoff
			$table->unsignedBigInteger('tipo_attivita_id')->nullable();  //ordinaria,straordinaria
			$table->unsignedTinyInteger('libero')->default(0);  // arriva da campo libero
			$table->foreign('tipo_impianto_id')->references('id')->on('tipo_impianto')->onDelete('SET NULL');
			$table->foreign('tipo_attivita_id')->references('id')->on('tipo_attivita')->onDelete('SET NULL');
		});
		Schema::create('attivita_impianti', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->date('user_at')->nullable();                		// quando
			$table->unsignedBigInteger('user_id'); 					// chi ha fatto
			$table->unsignedBigInteger('attivita_id');          		// (cosa) quale attivita      
			$table->unsignedBigInteger('impianto_id')->nullable();  	// (dove) su quale impianto
			$table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
			$table->foreign('attivita_id')->references('id')->on('attivita')->onDelete('SET NULL');           
			$table->foreign('impianto_id')->references('id')->on('impianti')->onDelete('SET NULL');
		});
		
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::disableForeignKeyConstraints();
		Schema::dropIfExists('lookup');
		Schema::dropIfExists('localita');
		Schema::dropIfExists('tipo_impianto');
		Schema::dropIfExists('impianti');
		Schema::dropIfExists('tipo_attivita');
		Schema::dropIfExists('attivita');
		Schema::dropIfExists('attivita_impianti');
    }
}
