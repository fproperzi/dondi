<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Models\Lookup;





class LookupSeeder extends Seeder
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
		
	    $keys = ['tag','tx1','tx2','tx3','tx4'];
		$values = array(
['acronimo','hom','home page','links per home'],
['acronimo','tat','tipo attivita'],
	['tat','conduzione ordinaria'],
	['tat','manutenzione ordinaria'],
	['tat','manutenzione straordinaria'],
	['tat','guasti anomalie'],
	['tat','manutenzione programmata'],
	
['acronimo','moi','tipi di manutenzione ordinarie Imhoff'],
	['moi','pulizia'],
	['moi','sfalcio'],
	['moi','asporti'],
	
['acronimo','msi','tipi di manutenzione straordinarie Imhoff'],
	['msi','cancello'],
	['msi','recinzione'],
	['msi','chiusini'],
	
['acronimo','cim','categorie impianto'],
	['cim','DP','Depuratore'],
	['cim','IM','Imhoff'],
	['cim','SF','Sfioratore'],
	['cim','SL','Sollevatore'],
	['cim','DI','Disabbiatore'],
	['cim','DE','Decantazione'],

['acronimo','tim','tipi impianto','categorie impianto'],
	['tim','Depuratore'		,'DP'],
	['tim','Disabbiatore'	,'DI'],
	['tim','I-Decantazione'	,'DE'],
	['tim','Imhoff'			,'IM'],
	['tim','Sfioratore'		,'SF'],
	['tim','Sfioratore_D'	,'SF'],
	['tim','Sfioratore_I'	,'SF'],
	['tim','Sfioratore_S'	,'SF'],
	['tim','Sollevamento'	,'SO'],
	
	

['acronimo','aas','aziende asporti'	],

['acronimo','cfa','nomi depuratori abili per conferimento'],
	['cfa','Paludi'],
	['cfa','Lana'],
	
	
['acronimo','rol','ruoli'		],
	['rol','TR'	,'Tecnico Responsabile'		],
	['rol','TS'	,'Tecnico Specializzato'		],
	['rol','OQ'	,'Operaio Qualificato'		],
	['rol','OG'	,'Operaio Generico'		],
	
['acronimo','sqd','Squadre Impianti'		],
	['sqd','A1'],
	['sqd','A2'],
	['sqd','A3'],				
	['sqd','B1'],
	['sqd','B2'],
	['sqd','B3'],
	['sqd','C1'],
	['sqd','C2'],
	['sqd','C3'],

['acronimo','cer','codice cer', "I rifiuti contrassegnati nell'elenco in rosso con un asterisco '*' sono rifiuti pericolosi ai sensi della direttiva 2008/98/CE relativa ai rifiuti pericolosi"],




		);

		// Loop through each user above and create the record for them in the database
		foreach ($values as $value) {
			lookup::create(array_combine($keys,array_pad($value,count($keys),null)));
		}

		Model::reguard();   
    }
}
