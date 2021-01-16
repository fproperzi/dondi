<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Conduzione;


class ConduzioneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Model::unguard();

		$values = array(

["DP","DP01","Pulizia area"						,"Pulizia continua dell'area coperta e/o scoperta degli impianti, di tutti i suoi manufatti (pozzetti, cunicoli, canalette, scale, passerelle, eccetera), di tutti i suoi fabbricati (locali tecnici, locali uso ufficio, servizi igienici, serramenti, finestre, eccetera), delle apparecchiature meccaniche ed elettromeccaniche, dello sfioratore a monte degli impianti (anche se ubicato all’esterno dell’area recintata o di pertinenza) e delle attrezzature compreso l’onere della raccolta del materiale di risulta e loro smaltimento."],
["DP","DP02","Sfalcio erba"						,"Taglio dell’erba a prato dell’area di pertinenza degli impianti per garantire le condizioni di sicurezza e il decoro degli impianti e comunque affinché, in tutto l’arco dell’anno, l’altezza dell’erba non raggiunga un’altezza maggiore a cinquanta (50) centimetri e non maggiore a venti (20) centimetri nelle zone di accesso agli impianti e nelle zone di passaggio per l’accesso ai manufatti."],
["DP","DP03","Potature"							,"Potatura delle siepi, dei cespugli, degli alberi e delle altre varietà arboree, presenti sull’area di pertinenza degli impianti ogni qualvolta si renda necessaria al fine di garantire una agevole viabilità all’interno degli impianti. Sono da ritenersi a carico della ditta anche l’eliminazione di eventuali arbusti, rampicanti o infestanti che invadano le recinzioni nonché eventuali rami"],
["DP","DP04","Sgombro neve"						,"Sgombero della neve all’interno dell’area di pertinenza degli impianti in grado di rendere ogni comparto accessibile e controllabile; ciò anche in occasione di accumuli nevosi eccezionali. Ove necessario lo sgombero della neve dovrà avvenire anche dalle falde dei tetti degli impianti al fine di non comprometterne l’integrità strutturale nel rispetto delle norme antinfortunistiche. Eventuali situazioni ritenute critiche dovranno essere immediatamente portate all’attenzione del Committente al fine di individuare idonee misure di risoluzione."],
["DP","DP05","Sgelamento condotte"				,"Sgelamento di qualsiasi condotta sia aree che sotterrane presenti all’interno degli impianti al fine di prevenire riduzione del funzionamento dell’impianto o rotture."],
["DP","DP06","Pulizia griglie e paratoie"			,"Pulizia a cadenza gestionale, e ogni qualvolta richiesto dal Committente, delle griglie e delle paratoie, con raccolta del vaglio in appositi big bag; ciò anche a seguito del verificarsi di eventi eccezionali quali, ad esempio, dell’arrivo all’impianto di acque reflue non assimilabili a quelle urbane o di intense precipitazioni piovose."],
["DP","DP07","Pulizia pretrattamento"				,"Pulizia ed espurgo dei comparti di pretrattamento (dissabbiatura e disoleatura) con raccolta delle sabbie, delle sostanze grasse e dei solidi galleggianti; ciò anche a seguito del verificarsi di eventi eccezionali quali, ad esempio, dell’arrivo all’impianto di acque reflue non assimilabili a quelle urbane o di intense precipitazioni piovose."],
["DP","DP08","Pulizia sedimentatore, pista ruote"	,"Pulizia a cadenza gestionale, e ogni qualvolta richiesto dal Committente, della canaletta del sedimentatore finale ed eventuali pertinenze. Pulizia della pista di scorrimento della ruota traente e di quella di folle."],
["DP","DP09","Assistenza spurghi"					,"Coordinamento ed assistenza al personale incaricato del servizio di spurgo, per tutto il tempo necessario al completamento dell’intervento, alle operazioni di asporto dei liquami, dei fanghi di supero prodotti, ed alla pulizia degli eventuali comparti di pretrattamento (dissabbiatura e disoleatura) con raccolta delle sabbie, delle sostanze grasse, dei solidi galleggianti e del vaglio a norma di legge."],
["DP","DP10","Disidratazione fanghi"				,"Disidratazione meccanica dei fanghi, ogni qualvolta necessiti, compresi gli oneri del caricamento dei fanghi disidratati e distribuzione in appositi cassoni con la finalità di ottenere un riempimento omogeneo e al massimo della capacità consentita. Accurata pulizia delle apparecchiature e dei relativi accessori (stazione preparazione polielettrolita, pompa fanghi, nastro trasportatore, ecc.) al termine di ogni operazione di disidratazione. "],
["DP","DP11","Preparazione reagenti"				,"Preparazione delle eventuali soluzioni dei reagenti chimici da utilizzare sia nei processi depurativi che per la disidratazione dei fanghi, compresa la manutenzione ordinaria delle relative apparecchiature elettromeccaniche."],
["DP","DP12","Scarico condensa vasca siidazione"	,"Scarico condensa compressori di servizio e soffianti volumetriche vasca ossidazione."],
["DP","DP13","Pulizia agitatori"					,"Pulizia almeno settimanale degli agitatori e mixer e ogni qualvolta richiesto dal Committente."],
["DP","DP14","Lubrificazione ed Ingrassaggio"		,"Costante lubrificazione, ingrassaggio, manutenzione e messa a punto delle apparecchiature elettromeccaniche e delle loro parti maggiormente sottoposte a sforzo meccanico (a mero titolo esemplificativo e non esaustivo: carroponti, nastropresse, centrifughe, griglie automatiche, coclee e nastri trasportatori); sono comprese le attività di smontaggio, di movimentazione, di pulizia e l’installazione ed avviamento a fine manutenzione."],
["DP","DP15","Assistenza interventi straordinari"	,"Assistenza in caso di interventi di manutenzione straordinaria sugli impianti effettuati da personale del Committente o da esso incaricato."],
["DP","DP16","Controllo scarico"					,"Controllo delle tubazioni di scarico, fino al corpo idrico ricettore, e del punto di scarico, almeno una volta ogni sei mesi e ogni qualvolta richiesto dal Committente; compresa il coordinamento e assistenza all’eventuale pulizia effettuata dal personale incaricato del servizio di spurgo"],
["DP","DP17","Verifica Impianti elettrici"		,"Verifica del perfetto stato di funzionalità, efficienza e pulizia degli impianti elettrici (a mero titolo esemplificativo e non esaustivo: apparecchiature, quadri, cavi, organi di comando e controllo, impianti di messa a terra, galleggianti di comando) con cadenza almeno mensile e ogni qualvolta richiesto dal Committente."],
["DP","DP18","Riattivazione dopo BlackOut"		,"Riattivazione, a perfetta regola d’arte, della funzionalità degli impianti a seguito dell’interruzione di erogazione dell’energia elettrica."],
["DP","DP19","Mantenimento strumenti"				,"Mantenimento in perfetto stato di funzionalità, efficienza, pulizia e taratura degli strumenti di controllo, regolazione e misura presenti negli impianti (misuratori di portata, sonde O2, NH4, SST, NO3 etc.)."],
["DP","DP20","Analisi ingresso e scarico"			,"Analisi a cadenza gestionale dei parametri pH, Redox, Conducibilità e temperatura in ingresso e allo scarico dell’impianto."],
["DP","DP21","Regolazioni"						,"Adozione di tutte le misure gestionali finalizzate al corretto, efficace ed efficiente funzionamento dell’impianto di depurazione, al trattamento di tutto il refluo in ingresso. Si intende compreso, a mero titolo esemplificativo e non esaustivo: la regolazione del carico idraulico, la gestione dei fanghi, le regolazioni funzionale delle apparecchiature elettromeccaniche, la gestione del trattamento dei rifiuti."],
["DP","DP22","Verifica fanghi"					,"Verifica bisettimanale e ogni qualvolta richiesto dal Committente del volume dei fanghi, in ossidazione e ricircolo, con cono imhoff a trenta minuti."],
["DP","DP23","Campionamento e registrazione"		,"Campionamento, registrazione delle misure in campo, redazione del verbale di campionamento e trasporto refrigerato nel luogo stabilito dal Committente nelle frequenze e nei modi previsti dal presente Capitolato, dalla normativa, dalle autorizzazioni allo scarico, dalla norma tecnica."],
["DP","DP24","Registrazione manutenzione"			,"Compilazione ad ogni visita gestionale dei registi di manutenzione, registrazione e all’occorrenza di carico e scarico."],
["DP","DP25","Registrazione parametri"			,"Trascrizione, sul quaderno di registrazione, dei parametri dei certificati analitici."],
["DP","DP26","Rilievo ore"						,"Rilievo delle ore di funzionamento delle apparecchiature elettromeccaniche e delle attività manutentive effettuate."],
["DP","DP27","Registrazione ENEL"					,"Lettura e trascrizione a registro dei consumi Enel."],
["DP","DP28","Revisione estintori"				,"Controllo delle scadenze di revisione semestrale, consegna e ritiro (presso la società convenzionata con il Committente/RUP), e ricollocazione presso il rispettivo luogo di appartenenza, degli estintori installati in tutti gli impianti di depurazione. È onere del Committente la revisione e/o il riempimento degli estintori."],
["DP","DP29","Cancello e recinzione"				,"Mantenimento in perfetto stato d’efficienza e funzionalità del cancello e della recinzione."],
["DP","DP30","Guerra Topi, animali nocivi"		,"Adozione di tutte le misure atte ad eliminare eventuali presenze di topi od altri animali nocivi effettuando le necessarie derattizzazioni o disinfestazioni."],
["DP","DP31","Rifiuti dell'impianto"				,"Carico e trasporto dei rifiuti prodotti dal ciclo depurativo previsti dal presente Capitolato in carico alla Ditta."],
["DP","DP32","Rifiuti non dell'impianto"			,"Carico, trasporto e smaltimento, in ottemperanza alle normative vigenti in materia, di tutti i rifiuti non prodotti dal ciclo depurativo e depositati nell’area di pertinenza degli impianti. "],
["DP","DP33","Coordinamento Dondi-BIM"			,"Attività di coordinamento tra la Ditta ed il Committente."],
["DP","DP34","Assistenza Ispettori"				,"Attività di assistenza agli ispettori degli Enti preposti al controllo (Provincia, ARPAV, ecc.)."],
["DP","DP35","Assistenza visitatori"				,"Attività di coordinamento ed assistenza dei visitatori agli impianti."],
["DP","DP36","Perimetrazione"						,"Attività di perimetrazione delle aree degli impianti oggetto di manutenzioni in caso dei sopralluoghi di cui ai precedenti tre punti. "],

["IM","IM01","Pulizia area"						,"Pulizia costante dell'area interna alla recinzione o, in sua assenza, dell’area su cui insiste il manufatto, dello sfioratore immediatamente a monte degli impianti (anche se ubicato all’esterno dell’area recintata o di pertinenza) compreso l’onere della raccolta del materiale di risulta."],
["IM","IM02","Sfalcio erba"						,"Taglio dell’erba a prato dell’area di pertinenza degli impianti per garantire le condizioni di sicurezza e il decoro degli impianti e comunque affinché, in tutto l’arco dell’anno, l’altezza dell’erba non raggiunga un’altezza maggiore a cinquanta (50) centimetri e non maggiore a venti (20) centimetri nelle zone di accesso agli impianti e nelle zone di passaggio per l’accesso ai manufatti."],
["IM","IM03","Potature"							,"Potatura delle siepi, dei cespugli, degli alberi e delle altre varietà arboree, presenti sull’area di pertinenza degli impianti ogni qualvolta si renda necessaria al fine di garantire una agevole viabilità all’interno degli impianti. Sono da ritenersi a carico della ditta anche l’eliminazione di eventuali arbusti, rampicanti o infestanti che invadano le recinzioni nonché eventuali rami o arbusti che presenti sulla viabilità di accesso all’impianto e che ne impediscano l’accessibilità anche ai mezzi di espurgo."],
["IM","IM04","Sgombro neve"						,"Sgombero della neve all’interno dell’area di pertinenza degli impianti in grado di rendere ogni comparto accessibile e controllabile; ciò anche in occasione di accumuli nevosi eccezionali."],
["IM","IM05","Sgelamento condotte"				,"Sgelamento di qualsiasi condotta sia aree che sotterrane presenti all’interno degli impianti al fine di prevenire riduzione del funzionamento dell’impianto o rotture."],
["IM","IM06","Controllo e pulizia Griglie"		,"Controllo e pulizia a cadenza gestionale, e ogni qualvolta richiesto dal Committente, delle griglie al fine di garantire il regolare afflusso delle acque reflue alla vasca Imhoff evitandone gli sfiori anche a seguito del verificarsi di eventi eccezionali quali, ad esempio, dell’arrivo alle vasche di acque reflue non assimilabili a quelle urbane o di intense precipitazioni piovose."],
["IM","IM07","Sedimentazione digestione"			,"Controllo a cadenza gestionale, e ogni qualvolta richiesto dal Committente, della sezione di sedimentazione con verifica ed eventuale movimentazione con opportuna sonda della fessura di comunicazione fra il comparto di sedimentazione e quello di digestione della vasca al fine di prevenire eventuale intasamenti;"],
["IM","IM08","Assistenza spurghi"					,"Coordinamento ed assistenza al personale incaricato del servizio di spurgo, per tutto il tempo necessario al completamento dell’intervento, alle operazioni di asporto dei liquami, dei fanghi, ed alla pulizia degli eventuali comparti di pretrattamento (dissabbiatura) con raccolta delle sabbie, delle sostanze grasse, dei solidi galleggianti a norma di legge."],
["IM","IM09","Assistenza interventi straordinari"	,"Assistenza in caso di interventi di manutenzione straordinaria sugli impianti effettuati da personale del Committente o da esso incaricato."],
["IM","IM10","Controllo scarico"					,"Controllo delle tubazioni di scarico, fino al corpo idrico ricettore, e del punto di scarico, almeno una volta ogni sei mesi e ogni qualvolta richiesto dal Committente; compresa il coordinamento e assistenza all’eventuale pulizia effettuata dal personale incaricato del servizio di spurgo"],
["IM","IM11","Cancello e recinzione"				,"Mantenimento in perfetto stato d’efficienza e funzionalità del cancello e della recinzione."],
["IM","IM12","Guerra Topi, animali nocivi"		,"Adozione di tutte le misure atte ad eliminare eventuali presenze di topi od altri animali nocivi effettuando le necessarie derattizzazioni o disinfestazioni. "],
["IM","IM13","Campionamento e registrazione"		,"Campionamento, registrazione delle misure in campo, redazione del verbale di campionamento e trasporto refrigerato nel luogo stabilito dal Committente nelle frequenze e nei modi previsti dal presente Capitolato, dalla normativa, dalle autorizzazioni allo scarico, dalla norma tecnica."],
["IM","IM14","Compilazione registri"				,"Compilazione, a cadenza almeno mensile, dei registi di manutenzione e all’occorrenza di carico e scarico rifiuti."],
["IM","IM15","Coordinamento Dondi-BIM"			,"Attività di coordinamento tra la Ditta ed il Committente."],
["IM","IM16","Assistenza Ispettori"				,"Attività di assistenza agli ispettori degli Enti preposti al controllo (Provincia, ARPAV, ecc.)."],
["IM","IM17","Assistenza visitatori"				,"Attività di coordinamento ed assistenza dei visitatori agli impianti."],
["IM","IM18","Perimetrazione"						,"Attività di perimetrazione delle aree degli impianti oggetto di manutenzioni in caso dei sopralluoghi di cui ai precedenti tre punti (IM15, IM16, IM17)."],
["IM","IM19","Rifiuti dell'impianto"				,"Carico, trasporto nonché smaltimento dei rifiuti prodotti dal ciclo depurativo previsti dal presente Capitolato in carico alla Ditta."],
["IM","IM20","Rifiuti non dell'impianto"			,"Carico, trasporto e smaltimento, in ottemperanza alle normative vigenti in materia, di tutti i rifiuti non prodotti dal ciclo depurativo e depositati nell’area di pertinenza degli impianti. "],

["SL","SL01","Segnaletica Stradale"				,"Posizionamento della segnaletica stradale prevista dalle normative vigenti in materia e dalle disposizioni del Committente; a lavori ultimati, i luoghi dovranno essere ripristinati allo stato d’origine pre-intervento. Apertura del chiusino del pozzetto del sollevamento e verifica della funzionalità del sistema dei galleggianti con loro eventuale pulizia. Pulizia di eventuali griglie in ingresso al pozzo."],
["SL","SL02","Sfalcio erba"						,"Taglio dell’erba a prato dell’area di pertinenza degli impianti per garantire le condizioni di sicurezza e il decoro degli impianti e comunque affinché, in tutto l’arco dell’anno, l’altezza dell’erba non raggiunga un’altezza maggiore a cinquanta (50) centimetri e non maggiore a venti (20) centimetri nelle zone di accesso agli impianti e nelle zone di passaggio per l’accesso ai manufatti."],
["SL","SL03","Potature"							,"Potatura delle siepi, dei cespugli, degli alberi e delle altre varietà arboree, presenti sull’area di pertinenza degli impianti ogni qualvolta si renda necessaria al fine di garantire una agevole viabilità all’interno degli impianti. Sono da ritenersi a carico della ditta anche l’eliminazione di eventuali arbusti, rampicanti o infestanti che invadano le recinzioni"],
["SL","SL04","Pulizia area"						,"Pulizia dell'area coperta e/o scoperta delle stazioni di sollevamento, di tutti i suoi manufatti (pozzetti, cunicoli, canalette, scale, passerelle, eccetera), dei fabbricati, delle apparecchiature, meccaniche ed elettromeccaniche, dello sfioratore a monte degli impianti (anche se ubicato all’esterno dell’area recintata o di pertinenza) e delle attrezzature compreso l’onere della raccolta del materiale di risulta."],
["SL","SL05","Sgombro neve"						,"Sgombero della neve all’interno dell’area recintata o di pertinenza affinché ogni comparto sia agevolmente accessibile e controllabile; ciò anche in occasione di accumuli nevosi eccezionali."],
["SL","SL06","Sgelamento condotte"				,"Sgelamento di qualsiasi condotta sia aree che sotterrane presenti all’interno degli impianti al fine di prevenire riduzione del funzionamento dell’impianto o rotture."],
["SL","SL07","Assistenza spurghi"					,"Coordinamento ed assistenza al personale incaricato del servizio di spurgo, per tutto il tempo necessario al completamento dell’intervento, alle operazioni di asporto dei liquami, delle sabbie, delle sostanze grasse e della pulizia del pozzo di alloggiamento delle pompe secondo le norme di legge, degli impianti ogni altra volta che si rendesse necessaria al fine di garantirne il loro regolare funzionamento."],
["SL","SL08","Verifica Catene sollevamento"		,"Verifica dello stato di efficienza e pulizia delle catene di sollevamento ed eventuale loro sostituzione se ammalorate su fornitura del Committente. Manutenzione ganci ancoraggio catene, staffe fissaggio tubi guida. "],
["SL","SL09","Verifica Impianti elettrici"		,"Verifica del perfetto stato di funzionalità, efficienza e pulizia degli impianti elettrici (con cadenza almeno mensile e ogni qualvolta richiesto dal Committente compresa sostituzione di lampade e componentistica minuta (a titolo esemplificativo e non esaustivo: fusibili e portafisibili, led. relè, interruttori). Riattivazione, a perfetta regola d’arte, della funzionalità degli impianti a seguito dell’interruzione di erogazione dell’energia elettrica."],
["SL","SL10","Strumenti misura/controllo"			,"Mantenimento in perfetto stato di funzionalità, efficienza e manutenzione degli eventuali strumenti di controllo, regolazione e misura (misuratori di livello, portata, etc.)."],
["SL","SL11","Condotte sollevamenti"				,"Controllo, compresa l’assistenza all’eventuale pulizia, delle condotte fognarie afferenti ai sollevamenti in caso di scarichi anomali o diminuzione delle portate in ingresso ai sollevamenti medesimi."],
["SL","SL12","Assistenza interventi straordinari"	,"Assistenza in caso di interventi di manutenzione straordinaria sugli impianti effettuati da personale del Committente o da esso incaricato."],
["SL","SL13","Cancello e recinzione"				,"Mantenimento in perfetto stato d’efficienza e funzionalità del cancello e della recinzione."],
["SL","SL14","Guerra Topi, animali nocivi"		,"Adozione di tutte le misure atte ad eliminare eventuali presenze di topi od altri animali nocivi effettuando le necessarie derattizzazioni o disinfestazioni."],
["SL","SL15","Coordinamento Dondi-BIM"			,"Attività di coordinamento tra la Ditta ed il Committente/RUP."],
["SL","SL16","Assistenza Ispettori"				,"Attività di assistenza agli ispettori degli Enti preposti al controllo (Provincia, ARPAV, ecc.)."],
["SL","SL17","Perimetrazione"						,"Attività di perimetrazione delle aree degli impianti oggetto di manutenzioni in caso dei sopralluoghi di cui ai precedenti due punti (SL15, SL16)."],
["SL","SL18","Rifiuti dell'impianto"				,"Carico e trasporto dei rifiuti prodotti dall’impianto previsti dal presente Capitolato in carico alla Ditta."],
["SL","SL19","Rifiuti non dell'impianto"			,"Carico, trasporto e smaltimento, in ottemperanza alle normative vigenti in materia, di tutti i rifiuti non prodotti dall’impianto e depositati nell’area di pertinenza degli impianti. "],

["SF","SF01","Segnaletica stradale"				,"Posizionamento della segnaletica stradale prevista dalle normative vigenti in materia e dalle disposizioni del Committente/RUP; a lavori ultimati, i luoghi dovranno essere ripristinati allo stato d’origine pre-intervento."],
["SF","SF02","Chiusino e condotta"				,"Apertura del chiusino del pozzetto di sfioro e verifica della funzionalità del sistema condotta fognaria/sfioratore di piena. Eventuali problematiche andranno segnalate tempestivamente al Committente."],
["SF","SF03","Avvertito per intervento"			,"Comunicare al Committente la necessità di effettuare la pulizia del sistema condotta fognaria/sfioratore di piena mediante l’impiego di autospurgo nei casi strettamente necessarie ovvero in caso di riscontrata attivazione dello sfioro in tempo secco."],
["SF","SF04","Controllo scarico"					,"Controllo, delle tubazioni di scarico, fino al corpo idrico ricettore, e del punto di scarico, almeno una volta ogni sei mesi e ogni qualvolta richiesto dal Committente;"],
["SF","SF05","Coordinamento Dondi-BIM"			,"Attività di coordinamento tra la Ditta ed il Committente/RUP."],
["SF","SF06","Assistenza Ispettori"				,"Attività di assistenza agli ispettori degli Enti preposti al controllo (Provincia, ARPAV, ecc.)."],
["SF","SF07","Perimetrazione"						,"Attività di perimetrazione delle aree degli impianti oggetto di manutenzioni in caso dei sopralluoghi di cui ai precedenti due punti (SF05, SF06)."],
["SF","SF08","Rifiuti dell'impianto"				,"Carico, trasporto dei rifiuti prodotti dall’impianto previsti dal presente Capitolato in carico alla Ditta. "],

		);
		$keys = ['cat','cdcdn','dscdn','txcdn'];
		// Loop through each user above and create the record for them in the database
		foreach ($values as $value) {
			Conduzione::create(array_combine($keys,array_pad($value,count($keys),null)));
		}

		Model::reguard();   
    }
}
