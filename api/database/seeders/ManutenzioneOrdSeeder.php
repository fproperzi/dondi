<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\ManutenzioneOrd;


class ManutenzioneOrdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {



		$values = array(
		
["PP","PP00","F0","controllo generale"],
["PP","PP01","F0","controllo  dello  stato  di  conservazione,  della  perfetta  tenuta,  supporto  guarnizioni  (compresa fornitura e sostituzione viti, rivetti e stessa guarnizione se visibilmente deteriorate)"],
["PP","PP02","F0","verifica  degli eventuali fine  corsa(meccanici/elettromeccanici)"],
["PP","PP03","F0","pulizie, lubrificazioni ed ingrassaggio parti scorrevoli e viti senza fine"],
["PP","PP04","M3","esecuzione di alcune manovre di apertura/chiusura fino a fine corsa FREQUENZA: trimestrale"],
["PM","PM00","F0","controllo generale"],
["PM","PM01","F0","controllo della stabilità delle griglie"],
["PM","PM02","M3","verifica tirafondi FREQUENZA:trimestrale"],
["PA","PA00","F0","controllo generale"],
["PA","PA01","F0","verifica funzionale del sistema grigliante con particolare attenzione alle spazzole e ai loro supporti nel caso di rotostracci e fitrococlee, alle guide e ai tappeti nel caso di griglie a gradini, alle ruote e ai sistemi di lavaggio nel caso di griglie a cestello rotante"],
["PA","PA02","W1","Lubrificazioni ed ingrassaggio settimanale ovvero intervalli più frequenti a seconda di quanto previsto dal costruttore della macchina, dalle condizioni di utilizzo e dalle ore di funzionamento"],
["PL","PL00","F0","controllo generale"],
["PL","PL01","F0","accurata pulizia;"],
["PL","PL02","F0","verifica funzionale dei galleggianti di start, stop, consenso seconda pompa, etc."],
["PL","PL03","W1","fornitura e sostituzione in caso di malfunzionamento FREQUENZA:  settimanale"],
["PE","PE00","F0","controllo generale"],
["PE","PE01","F0","estrazione, accurata pulizia o ripristino in caso di otturazione"],
["PE","PE02","F0","verifica stato organismi di moto"],
["PE","PE03","M6","verifica livello e stato dell’olio e rabbocco frequenza: semestrale ovvero intervalli più frequenti a seconda di quanto previsto dal costruttore della macchina, dalle condizioni di utilizzo e dalle ore di funzionamento o in caso di riscontrata frequente otturazione"],
["EA","EA00","F0","controllo generale"],
["EA","EA01","W1","accurata pulizia del corpo, dell’elica e del cavo di sospensione FREQUENZA:  settimanale"],
["OV","OV00","F0","controllo generale"],
["OV","OV01","F0","controllo dei giunti d’accoppiamento"],
["OV","OV02","F0","verifica livello dell’olio lubrificante ed eventuale rabbocco"],
["OV","OV03","F0","verifica stato manicotti mandata e fascette di contenimento"],
["OV","OV04","M1","ingrassaggio e lubrificazione FREQUENZA:   mensile"],
["OD","OD00","F0","controllo generale"],
["OD","OD01","F0","controllo giunti d’accoppiamento e tasselli giunti elastici tra motore e albero"],
["OD","OD02","F0","controllo catena di trasmissione, stato di conservazione pignone e corona. Pulizia e lubrificazione catena."],
["OD","OD03","F0","verifica stato biodisco ed eventuali anomalie nella rotazione dovute a sbilanciamento della biomassa  adesa"],
["OD","OD04","M2","verifica e lubrificazione albero primario e relativi cuscinetti FREQUENZA: bimestrale"],
["SC","SC00","F0","controllo generale"],
["SC","SC01","F0","controllo giunti d’accoppiamento e tasselli giunti elastici"],
["SC","SC02","F0","verifica funzionale finecorsa e sistema alza/abbassa raschie (se presente)"],
["SC","SC03","F0","ingrassaggio mozzi ruote traenti, di folle e di contrasto"],
["SC","SC04","F0","controllo e lubrificazione dei carrelli di scorrimento del cavo a festone"],
["SC","SC05","M2","verifica livello e stato olio lubrificante FREQUENZA:   bimestrale"],
["CS","CS00","F0","controllo generale"],
["CS","CS01","M1","controllo della tenuta del serbatoio; FREQUENZA:   mensile"],
["CP","CP00","F0","controllo generale"],
["CP","CP01","F0","pulizia iniettore"],
["CP","CP02","F0","pulizia pescante"],
["CP","CP03","F0","verifica funzionamento galleggiante di minimo livello se presente"],
["CP","CP04","F0","controllo stato tubazioni di aspirazione e mandata"],
["CP","CP05","F0","controllo dei serraggi, dell’alimentazione e della morsettiera"],
["CP","CP06","F0","verifica  volume  iniettato  per  unità  di  tempo  con  bicchiere  graduato  ed  eventuale  correzione taratura mensile"],
["FP","FP00","F0","controllo generale"],
["FP","FP01","F0","accurata pulizia e disossidazione dei vari componenti"],
["FP","FP02","F0","controllo baderne"],
["FP","FP03","M2","controllo del livello della qualità dell’olio lubrificante FREQUENZA:   bimestrale"],
["FD","FD00","F0","controllo generale"],
["FD","FD01","F0","controllo dei rulli, dei cilindri, del nastro e dei raschiatori"],
["FD","FD02","F0","controllo della funzionalità degli “allineatori” del telo"],
["FD","FD03","F0","controllo dell’integrità del telo/correttezza parametri centrifuga"],
["FD","FD04","F0","controllo dei rulli, dei cilindri, del nastro e dei raschiatori"],
["FD","FD05","F0","controllo delle coclee di scarico disidrato"],
["FD","FD06","F0","smontaggio e pulizia cassetti di lavaggio teli e dei relativi ugelli"],
["FD","FD07","W1","scarico condensa compressore aira di servizio FREQUENZA:  ad  ogni  avvio  della  macchina  oppure,  nel  periodo  di  disidratazione,  almeno settimanale"],
["FD","FD08","M2","verifica livello dell’olio motoriduttore bimestrale o iferiori dipende uso h/lavoro"],
["FD","FD09","W1","ingrassaggi e lubrificazioni settimanale o più frequenti uso-manuale"],
["FL","FL00","F0","controllo generale"],
["FL","FL01","F0","verifica pressione mandata"],
["FL","FL02","F0","verifica stato baderne"],
["FL","FL03","M6","ingrassaggi e lubrificazioni FREQUENZA:  semestrale"],
["FT","FT00","F0","controllo generale"],
["FT","FT01","F0","controllo integrità del nastro in gomma e del suo tensionamento"],
["FT","FT02","F0","controllo dello stato d’usura delle spazzole coclea"],
["FT","FT03","M6","ingrassaggi e lubrificazioni FREQUENZA: semestrale. Ingrassaggio settimanale ovvero intervalli più frequenti a seconda di quanto previsto dal costruttore della macchina, dalle condizioni di utilizzo e dalle ore di funzionamento"],
["FC","FC00","F0","controllo generale"],
["FC","FC01","M6","controllo funzionamento e taratura pressostati semestrale"],
["FC","FC02","M6","controllo raccorderia, tubi e gruppi distributori aria semestrale"],
["FC","FC03","M6","controllo cinghie di trasmissione ed eventuale fornitura e sostituzione semestrale"],
["FC","FC04","M1","verifica livello dell’olio e dello stato del filtro mensile"],
["FC","FC05","W1","scarico della condensa almeno una volta a settimana ovvero  intervalli più frequenti nel caso se ne ravveda la necessità"],
["EQ","EQ00","F0","controllo generale"],
["EQ","EQ01","F0","verifica funzionamento interruttore differenziale"],
["EQ","EQ02","F0","verifica stato di conservazione cavi (corrosione, passaggio di roditori etc.)"],
["EQ","EQ03","F0","controllo della taratura e funzionamento degli interruttori termici"],
["EQ","EQ04","F0","verifica serraggio viteria"],
["EQ","EQ05","F0","pulizia interna del quadro con aspiratore e prodotti idonei con particolare attenzione ai quadri inverter"],
["EQ","EQ06","F0","verifica ed eventuale fornitura e sostituzione fusibili"],
["EQ","EQ07","F0","verifica ed eventuale fornitura e sostituzione lampadine di segnalazione"],
["EQ","EQ08","F0","verifica funzionamento ed efficacia ventole di raffreddamento"],
["EQ","EQ09","F0","verifica ed eventuale fornitura e sostituzione materiale filtrante sistemi di aerazione forzata"],
["EQ","EQ10","M3","verifica funzionale dei pulsanti di sgancio/emergenza FREQUENZA: trimestrale"],
["ER","ER00","F0","controllo generale"],
["ER","ER01","F0","controllo delle dispersioni ed eventuali interventi di riparazione"],
["ER","ER02","F0","controllo del cos φ"],
["ER","ER03","M3","misura di capacità residua sui condensatori dei gruppi rifasatori FREQUENZA: trimestrale"],
["EL","EL00","F0","controllo generale"],
["EL","EL01","F0","verifica funzionamento ed eventuale fornitura e sostituzione lampade"],
["EL","EL02","G0","verifica funzionale delle lampade di emergenza e stato delle batterie FREQUENZA: cadenza gestionale"],
["EG","EG00","F0","controllo generale"],
["EG","EG01","F0","controllo livello carburante ed eventuale rabbocco dopo ogni  fuzionamento "],
["EG","EG02","F0","controllo livello olio"],
["EG","EG03","F0","avviamento in bianco"],
["EG","EG04","M1","controllo stato batterie. Eventuale rabbocco acqua distillata e ricarica FREQUENZA: mensile"],
["IS","IS00","F0","controllo generale"],
["IS","IS01","F0","controllo  della  perfetta  efficienza  e  stato  di  conservazione  di  paranchi,  catene  e  funi  di sollevamento."],
["IS","IS02","M3","compilazione del registro di controllo FREQUENZA:  trimestrale"],
["FI","FI00","F0","controllo generale"],
["FI","FI01","G0","verifica generale stato funzionamento del sistema e frequenza lavaggi FREQUENZA: cadenza gestionale"],
["DU","DU00","F0","controllo generale"],
["DU","DU01","F0","verifica generale stato funzionamento del sistema"],
["DU","DU02","F0","verifica del funzionamento delle lampade"],
["DU","DU03","G0","eventuale sostituzione delle lampade quarzi e or FREQUENZA:  cadenza  gestionale"],
["MP","MP00","F0","controllo generale"],
["MP","MP01","M3","controllo generale e verifica taratura FREQUENZA: trimestrale"],
["SN","SN00","F0","controllo generale"],
["SN","SN01","W2","accurata pulizia delle sonde e dei misuratori FREQUENZA:  bisettimanale"]


		);
		$keys = [
			'cdcmo',  // codice categoria manutenzione ordinaria
			'cdmao',  // codice manutenzione ordinaria
			'cdfrq',  // codice frequenza manutenzione
			'txcmo'   // descrizione manutenzione ordinaria
		];
		// Loop through each user above and create the record for them in the database
		foreach ($values as $value) {
			ManutenzioneOrdSeeder::create(array_combine($keys,array_pad($value,count($keys),null)));
		}

    }
}