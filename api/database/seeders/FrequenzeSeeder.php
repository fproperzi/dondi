<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Frequenza;


class FrequenzaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {



		$values = array(
		
//Frequenza azione 
["F0",  0,"alla Presenza"],
["F1",  0,"alla Bisogna"],
["M1", 30,"Mensile"],
["M2", 60,"Bimestrale"],
["M3", 90,"trimestrale"],
["M6",180,"Semestrale"],
["W1",  7,"Settimanale"],
["W2", 14,"Bisettimanale"],
["G0",  0,"Cambio gestione"],


		);
		$keys = [
			'cdfrq',  // codice frequenza
			'ggfrq',  // giorni frequenza, 0= particolare
			'dsfrq'   // descrizione
		];
		// Loop through each user above and create the record for them in the database
		foreach ($values as $value) {
			Frequenza::create(array_combine($keys,array_pad($value,count($keys),null)));
		}

    }
}