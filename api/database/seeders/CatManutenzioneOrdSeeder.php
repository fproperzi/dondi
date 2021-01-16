<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\CatManutenzioneOrd;


class CatManutenzioneOrdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


		$values = array(
		
["PP","Primari"			,"Paratoie – Saracinesche – Valvole"],
["PM","Primari"			,"Grigliatura Manuale"],
["PA","Primari"			,"Grigliatura Automatica"],
["PL","Primari"			,"Interruttori Di Livello/Galleggianti"],
["PE","Primari"			,"Elettropompa Sommersa"],
["EA","Ossidazione"		,"Elettroagitatore Sommerso"],
["OV","Ossidazione"		,"Soffiante Volumetrica"],
["OD","Ossidazione"		,"Biodisco/Biorullo"],
["SC","Sedimentazione"	,"Carroponte Sedimentatore"],
["CS","Chimici"			,"Serbatoio Reagente Chimico"],
["CP","Chimici"			,"Pompe Dosatrici Reagenti Chimici"],
["FP","Fanghi"			,"Elettropompa Monovite"],
["FD","Fanghi"			,"Disidratazione Meccanica Dei Fanghi"],
["FL","Fanghi"			,"Elettropompa Lavaggio Teli (Multistadio) e Pompe Fuori Acqua Anche Antiincendio"],
["FT","Fanghi"			,"Nastro Trasportatore/ Coclea"],
["FC","Fanghi"			,"Compressori Aria Di Servizio"],
["EQ","Elettrici"		,"Quadro Elettrico Di Controllo e Comando"],
["ER","Elettrici"		,"Impianti Elettrici e Rifasatori"],
["EL","Elettrici"		,"Corpi Illuminanti"],
["EG","Elettrici"		,"Gruppi Elettrogeni e Antincendio A Scoppio"],
["IS","Impianti"		,"Impianti Di Sollevamento"],
["FI","Filtrazione"		,"Sistemi Di Filtrazione"],
["DU","Disinfezione"	,"Sistemi Disinfezione UV"],
["MP","Misuratore"		,"Misuratore Di Portata"],
["SN","Sonde"			,"Sonde E Strumenti Di Misura Fissi"]

		);
		$keys = [
			'cdcmo',	// codice categoria manutenzione ordinaria
			'dscmo',    // descrizione breve-brevissima
			'txcmo',	// descrizione
		];
		// Loop through each user above and create the record for them in the database
		foreach ($values as $value) {
			CatManutenzioneOrd::create(array_combine($keys,array_pad($value,count($keys),null)));
		}

    }
}