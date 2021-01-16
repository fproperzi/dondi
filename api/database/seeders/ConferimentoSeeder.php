<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Models\Conferimento;





class ConferimentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Model::unguard();

		//DB::table('lookup')->delete();
		
	    $keys = ['titolare','impianto','note'];
		$values = array(
["ETRA S.p.A."	,"CARMIGNANO DI BRENTA (PD) – via Ospitale"	,"autorizzazione n. 694 del 24/05/2011 Orari: dal Lunedi al Venerdì dalle 8.00 alle 11.30 e dalle 13.30 alle 15.30 il sabato dalle 8.30 alle 11.30"],
["ETRA S.p.A."	," VIGONZA - via San Gregorio Barbarigo"	,"autorizzazione n. 3150/dep/2016 del 06/04/2016 Orari: dal Lunedi al Venerdì dalle 8.00 alle 12.00 e dalle 14.00 alle 16.30 Non si effettua lavaggio autocisterna"],
["ETRA S.p.A."	,"CITTADELLA (PD) – via Sansughe"			,"autorizzazione n. 2926/dep/2013 del 20/12/2013 Orari: dal Lunedi al sabato dalle 8.00 alle 11.30 Limitazione di accesso per i rimorchi e bilici causa ristretta manovra di accesso, inoltre tutti i bottini devono essere già pesati, l’accesso è per via Bellinghiera"],
["ETRA S.p.A."	,"LIMENA (PD) - via A. Volta"				,"autorizzazione n. 5306/ec/2008 del 18/02/2009 Orari: da Lunedi al Venerdì dalle 8.00 alle 11.30 e dalle 13.30 alle 16.00 il sabato dalle 8.00 alle 11.30"],
["VEOLIA ACQUA SERVIZI S.R.L."	,"PAESE (TV) – via Brondi"	,"decreto n. 116 del 24/12/2012 BIM GSP S.p.A. BELLUNO (BL) – loc. Marisiga – via Col Da Ren autorizzazione prot. 1591/ECO del 09/01/2007 accesso possibile solo con automezzi tipo B, C, D, E"],
["BIM GSP S.p.A.","PONTE NELLE ALPI (BL) – loc. La Nà – via Dei Zattieri","determinazione n. 1542 del 05/09/2013 accesso possibile solo con automezzi tipo B, C, D, E"],
["BIM GSP S.p.A.","ALPAGO (BL) - loc. Paludi - via dell’Industria","in corso di autorizzazione"],
		



		);

		// Loop through each user above and create the record for them in the database
		foreach ($values as $value) {
			Conferimento::create(array_combine($keys,array_pad($value,count($keys),null)));
		}

		Model::reguard();   
    }
}
