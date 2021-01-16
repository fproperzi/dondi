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
		
		Schema::create('interventi', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->datetime("in_at");
			$table->datetime("out_at")->nullable();
			$table->unsignedBigInteger("user_id");
			$table->unsignedBigInteger("impianto_id");
			
			$table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
			$table->foreign('impianto_id')->references('id')->on('impianti')->onDelete('SET NULL')->onUpdate('SET NULL');
		});
		
		Schema::create('interventi_foto', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string("foto");
			$table->unsignedBigInteger("intervento_id");
			$table->foreign('intervento_id')->references('id')->on('interventi');
		});
		
		Schema::create('interventi_asporti', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string("cdcer")->nullable();
			$table->string("mq")->nullable();
			$table->string("note")->nullable();
			$table->unsignedBigInteger("azienda_id");  // azienda prelievo
			$table->unsignedBigInteger("conferimento_id");  // impianto di conferimento
			$table->unsignedBigInteger("intervento_id");
			$table->foreign('cdcer')->references('cdcer')->on('codici_cer');
			$table->foreign('azienda_id')->references('id')->on('aziende_asporti');
			$table->foreign('conferimento_id')->references('id')->on('conferimento_impianti');
			$table->foreign('intervento_id')->references('id')->on('interventi');
		});
				Schema::create('codici_cer', function (Blueprint $table) {
					$table->id();
					$table->timestamps();
					$table->string('cdcer')->unique();
					$table->string('dscer')->nullable();
					$table->unsignedTinyInteger('bconferimento')->default(0);  //da usare per dondi
				});
						// aziende che fanno gli asporti
				Schema::create('aziende_asporti', function (Blueprint $table) {
					$table->id(); 
					$table->timestamps();
					$table->string("titolare")->nullable();
					$table->string("impianto");		
					$table->string("note");		
				});
						// luogi dove conferire gli asporti
				Schema::create('conferimento_impianti', function (Blueprint $table) { 
					$table->id(); 
					$table->timestamps();
					$table->string("titolare")->nullable();
					$table->string("impianto");		
					$table->string("note");		
				});
		
		Schema::create('interventi_letture', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string("cono")->nullable();
			$table->string("enel")->nullable();
			$table->string("vasca")->nullable();
			$table->string("note")->nullable();
			$table->unsignedBigInteger("intervento_id");
			$table->foreign('intervento_id')->references('id')->on('interventi');
		});
		//conduzioni ordinarie
		Schema::create('interventi_cnd_ordinarie', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->unsignedBigInteger("conduzione_id");
			$table->foreign('conduzione_id')->references('id')->on('conduzioni_ordinarie');
			$table->unsignedBigInteger("intervento_id");
			$table->foreign('intervento_id')->references('id')->on('interventi');
		});
				// luogi dove conferire gli asporti
				Schema::create('conduzioni_ordinarie', function (Blueprint $table) { 
					$table->id(); 
					$table->timestamps();
					$table->string("note");		
				});	
				
		//manutenzioni ordinarie
		Schema::create('interventi_mnt_ordinarie', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->unsignedBigInteger("intervento_id");
			$table->foreign('intervento_id')->references('id')->on('interventi');
		});
				
		
		//manutenzioni straordinarie
		Schema::create('interventi_straordinarie', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->unsignedBigInteger("intervento_id");
			$table->foreign('intervento_id')->references('id')->on('interventi');
		});
		
		//guasti, anomaliae
		Schema::create('interventi_guastianomalie', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->unsignedBigInteger("intervento_id");
			$table->foreign('intervento_id')->references('id')->on('interventi');
		});
		
		//manutenzioni programmate
		Schema::create('interventi_programmate', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->unsignedBigInteger("intervento_id");
			$table->foreign('intervento_id')->references('id')->on('interventi');
		});
		
		
//lookup table		



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

		
		Schema::create('lookup', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('tag')->index();  // cer, conferimenti,azconf:aziendaconferimeti, 
			$table->string('info1');
			$table->string('info2')->nullable();
			$table->string('info3')->nullable();
			$table->string('info4')->nullable();

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



		
		Schema::create('impianti_foto', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->unsignedBigInteger('impianto_id');
			$table->unsignedBigInteger('foto_id');
			$table->foreign('impianto_id')->references('id')->on('impianti')->onDelete('SET NULL');
			$table->foreign('foto_id')->references('id')->on('foto')->onDelete('SET NULL');

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
