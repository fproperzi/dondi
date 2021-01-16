
// stateModule.changeState("newstate"); //sets the state
// var theState = stateModule.getState(); //gets the state
// template literal ->  [1,2,3,4,'<'].reduce( (w,k)=>(`${w}<li><h2>${ htmlEntities(k) }</h2></li>`),'' );

var gtmpls = {
	'test1' : '<h1> hallo {{=name}}!',
	'test2' : '{{ _.each([1,2,3,4,5],function(i){  }}<li><h2>{{=i}}</h2></li>{{ } }}'
}
function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
var mswitch = (function () {
	var r,bRspOK; 	// response & response is Ok
	var tmpls = {}, 						//cache templates
		pub = {};							// public object - returned at end of module

	const gotoPage = function(i){
		$( ":mobile-pagecontainer" ).pagecontainer( "change",i);
	};
	pub.ajax = function(a,j,cb,gp){  //a=action, j={data}, cb = callback, gp= 'GET' || 'POST'
			j = j||{};
			a = a||sfVal4Serialized(j,'action');
		
		if(!a) return vfPopUp('Error','Azione non riconosciuta');
		
		vfTackEvent( 'action:'+ a );

		$.ajax({
			  data : j, 
			   url : j._url || $.mobile.path.getDocumentUrl(), //$.mobile.path.getDocumentBase(true).pathname,
			  type : gp || sfVal4Serialized(d,'_type') || sfVal4Serialized(d,'_method') || 'GET',                  
			 async : 'true',
		  dataType : 'json',
		beforeSend : vfWaitOn,
		  complete : vfWaitOff,
		   success : function(r) { 
		   		vfCallBack(aa,r); 
		   	},
			 error : vfjqxKoPop //jqXHR, textStatus, errorThrown
		}); // ajax      
	};
	pub.tmpls = function(t,j){
		if(tmpls[t] === void(0)) {
			if(t.charAt(0) === '#' && $(t)) tmpls[t] = _.template( $(t).html() , { 'variable': 'data' });	// arriva da script type="text/template"  id="t"
			else if(gtmpls[t])				tmpls[t] = _.template( gtmpls[t]   , { 'variable': 'data' });		// arriva da globale
			else							tmpls[t] = t+": template non disponibile";
		}
		if(j && _.isFunction(tmpls[t])) return tmpls[t](j);
		else                            return tmpls[t];					// template pronto
	}
	pub.setRsp = function(rsp) {		// response
		r = rsp || {};
		bRspOK = _.get(r,'status') === true || _.get(r,'Result') ==='OK';
	};
	pub.getRsp = function() {
			return r;
	};

	pub['p-ana'] = function() {     
	//$.get(URL,data,function(data,status,xhr),dataType)
		$.get($.mobile.path.getDocumentUrl(),{'action':'p-ana'},function(data,status,xhr){
			
			r = data || {};
			//if( _.get(r,'status') === true || _.get(r,'Result') ==='OK') {
			if( r['status'] === true || r['Result'] ==='OK') {
				let j = { 
					jana : r.ana
				}		
				$("#p-ana_li-ana").empty().append( pub.tmpls('#tmpl_p-ana_li-ana',j) ).listview("refresh");
			}
			else vfRspKoPop(r);
		},'json');		
	};
	
	pub['p-ana'] = function() {     

		$.get($.mobile.path.getDocumentUrl(),{'action':'p-ana'},function(data,status,xhr){
			
			r = data || {};
			//if( _.get(r,'status') === true || _.get(r,'Result') ==='OK') {
			if( r['status'] === true || r['Result'] ==='OK') {
				let j = { 
					jana : r.ana
				}		
				$("#p-ana_li-ana").empty().append( pub.tmpls('#tmpl_p-ana_li-ana',j) ).listview("refresh");
			}
			else vfRspKoPop(r);
		},'json');		
	}
	
	pub['agenti-rf'] = pub['p-ana'];
	pub['p-dst'] = function() {
		let p = $.mobile.pageData||{}; //_.get($.mobile,'pageData',{});
		if(!p.ana || !r.dst) {$.mobile.changePage( "#p-ana"); return;}
		let j = {
			jdst : _.filter(r.dst,function(v){return v.cdana===parseInt(p.ana) && v.dsdst.length }) // nordiconad problem
		}
		$("#p-dst_li-dst").empty().append( pub.tmpls('#tmpl_p-dst_li-dst',j) ).listview("refresh");
	};
	pub['p-ordrf'] = function() {		// creazione form ordini
		let p = $.mobile.pageData||{}; //_.get($.mobile,'pageData',{});
		if(!p.ana || !r.anart) {$.mobile.changePage( "#p-ana"); return;}  //non ho p.ana torno indietro
		$('#p-ordrf_a-dst').attr("href",'#p-dst?ana='+p.ana);		  //link to back
		
		let j = {
			 mailage : 'fproperzi@gmail.com' //'<?= $_SESSION['email'] ?>'
			,nameage : 'kino' //'<?= $_SESSION['name'] ?>'
			,cdage : ''
			,cdana : p.ana
			,dsana : _.filter(r.ana,function(v){return v.cdana===parseInt(p.ana)}).map(function(v){return v.dsana+' '+v.ind})[0]
			,cddst : p.dst
			,dsdst : _.filter(r.dst,function(v){return v.cddst===parseInt(p.dst)}).map(function(v){return v.dsdst+' '+v.ind})[0]
			,dtord : new Date().toISOString().split('T')[0] //2020-12-31
			,nuord : orderId()
			,cdmag : r.mag  	//magazzino
			,cdvtt : r.vtt	//vettore
			,jddt  : _.filter(r.ddt,function(v){return v.cddst===parseInt(p.dst)})
			,jart  : _.filter(r.anart,function(v){return v.cdana===parseInt(p.ana)})
					  .reduce(function(s,v){if(r.art[v.cdart]) s.push( r.art[v.cdart]); return s;},[])
			//_.filter(r.anart,function(v){return v.cdana===parseInt(p.ana)}).map(function(v){return r.art[v.cdart]})
		}
		$("#p-ordrf_frm").empty().append( pub.tmpls('#tmpl_p-ordrf_frm',j) ).enhanceWithin(); //trigger( "create" ).listview("refresh");
	};	
	pub['p-ordrf_frm-go'] = function() {
	
		let j,i, e = '', b = false, f = 'form#p-ordrf_frm ';  //id form
	
		i = 'numord'; if( $(f+'input[name="'+i+'"]').val().trim().length == 0 ) e += '&#x274C; Il "Num ordine cliente" è obbligatorio.\n';
		i = 'dteva';  if( $(f+'input[name="'+i+'"]').val().trim().length == 0 ) e += '&#x274C; La "Data prevista consegna" è obbligatoria.\n';
		

		$(f+'.cart,coma').each(function(i){
			if ($(this).val().trim().length != 0) b = true;
		});
		if(!b) e += '&#x274C; Su tutti gli articoli non è stata inserita alcuna quantità di Vendita od Omaggio.\n';
		
		if(e.length) vfPopUp('Errore',e+'\n{{#p-ordrf:back:Correggi:}}' );
		else {
			j = _.filter($(f).serializeArray(),function(v,k,l){return v.value.length!==0});
			//URL,data,function(data,status,xhr),dataType
			$.post($.mobile.path.getDocumentUrl(),j,function(rsp,status,xhr){
				console.log(status);
				alert(rsp.message);
			},'json');
			
		}
	};
	pub['frm_ord_done'] = function() {	   
		if(bRspOK) $.mobile.changePage( "#p-done");
		else       vfRspKoPop(r,'{{#p-ordrf:back:Riprova:}}');					
	};
	pub['ordine_rf'] = pub['frm-ord-done'];

	return pub; // expose externally
}());