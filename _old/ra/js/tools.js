  
/* JavaScript Cookie v2.1.4
 * https://github.com/js-cookie/js-cookie
**/
!function(e){var n=!1;if("function"==typeof define&&define.amd&&(define(e),n=!0),"object"==typeof exports&&(module.exports=e(),n=!0),!n){var o=window.Cookies,t=window.Cookies=e();t.noConflict=function(){return window.Cookies=o,t}}}(function(){function e(){for(var e=0,n={};e<arguments.length;e++){var o=arguments[e];for(var t in o)n[t]=o[t]}return n}function n(o){function t(n,r,i){var c;if("undefined"!=typeof document){if(arguments.length>1){if(i=e({path:"/"},t.defaults,i),"number"==typeof i.expires){var a=new Date;a.setMilliseconds(a.getMilliseconds()+864e5*i.expires),i.expires=a}i.expires=i.expires?i.expires.toUTCString():"";try{c=JSON.stringify(r),/^[\{\[]/.test(c)&&(r=c)}catch(e){}r=o.write?o.write(r,n):encodeURIComponent(String(r)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),n=encodeURIComponent(String(n)),n=n.replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent),n=n.replace(/[\(\)]/g,escape);var f="";for(var s in i)i[s]&&(f+="; "+s,i[s]!==!0&&(f+="="+i[s]));return document.cookie=n+"="+r+f}n||(c={});for(var p=document.cookie?document.cookie.split("; "):[],d=0;d<p.length;d++){var u=p[d].split("="),l=u.slice(1).join("=");'"'===l.charAt(0)&&(l=l.slice(1,-1));try{var g=u[0].replace(/(%[0-9A-Z]{2})+/g,decodeURIComponent);if(l=o.read?o.read(l,g):o(l,g)||l.replace(/(%[0-9A-Z]{2})+/g,decodeURIComponent),this.json)try{l=JSON.parse(l)}catch(e){}if(n===g){c=l;break}n||(c[g]=l)}catch(e){}}return c}}return t.set=t,t.get=function(e){return t.call(t,e)},t.getJSON=function(){return t.apply({json:!0},[].slice.call(arguments))},t.defaults={},t.remove=function(n,o){t(n,"",e(o,{expires:-1}))},t.withConverter=n,t}return n(function(){})});  
/* pass parameters: #newpage?action=test&test=1234  => $.mobile.pageData = {action:'test',test:1234}  
 * https://github.com/jblas/jquery-mobile-plugins/blob/master/page-params/jqm.page.params.js 
!function($,e,o){$(document).on("pagebeforechange",function(e,t){if("string"==typeof t.toPage){var p=$.mobile.path.parseUrl(t.toPage);if($.mobile.path.isEmbeddedPage(p)){var i=$.mobile.path.parseUrl(p.hash.replace(/^#/,""));if(i.search){var r,n,s,l,g=(i.search||"").replace(/^\?/,"").split(/&/),h={};for(r=0;r<g.length;r++){var f=g[r];f&&(n=f.split(/=/),s=n[0],l=n[1],h[s]===o?h[s]=l:("object"!=typeof h[s]&&(h[s]=[h[s]]),h[s].push(l)))}t.options.dataUrl||(t.options.dataUrl=t.toPage),t.options.pageData=h,t.toPage=p.hrefNoHash+"#"+i.pathname}$.mobile.pageData=t&&t.options&&t.options.pageData?t.options.pageData:null}}})}(jQuery,window);

**/



!function($, e) {
    $(document).on("pagebeforechange", function(e, t) {
        if ("string" == typeof t.toPage) {
			var r = jfGetParams(t.toPage);
			if(r.isEmbeddedPage) {
				t.toPage = r.toPage;
				$.mobile.pageData = r.pageData;
			}
        }
    });
	// Bind to "mobileinit" before you load jquery.mobile.js
	$( document ).on( "mobileinit", function(e) {
		$.mobile.loader.prototype.options.text = _waiting_please;
		$.mobile.loader.prototype.options.textVisible = true;
		$.mobile.defaultPageTransition = "slide";
		$.mobile.dynamicBaseEnabled = false;
		//$.mobile.pushStateEnabled = false;
		$.mobile.listview.prototype.options.autodividersSelector = function( e ) {
			var s = $.trim( e.text() ) || null;
				 if ( !s )                       return null;
			else if ( !isNaN(parseFloat(text)) ) return "0-9";
			else 								 return s.slice( 0, 1 ).toUpperCase();
		};
		
		//login as condition  https://jqmtricks.wordpress.com/2014/12/29/redirect-on-start-up/
		if (false) {
			$.mobile.autoInitializePage = false;
			$(function() {  //jq ready after mobile init
				//make login page div first one in DOM 
				
				// multipage model: 
				$("#login").prependTo("body");  
				$.mobile.initializePage();
				
				//single page model:  replace current page div with another page loaded externally
				$("body").load("login.html [data-role=page]", function(data) {
					$.mobile.initializePage();
				});
			});
		}
		
	});
}(jQuery, window);
//--- get params and other info 
function jfGetParams(url) {
	var h= {}
	   ,p = $.mobile.path.parseUrl(url)
	   ,i = $.mobile.path.parseUrl(p.hash.replace(/^#/, ""));

	if (i.search) {
		var j, n, s, l, g = (i.search || "").replace(/^\?/, "").split(/&/);
		for (j = 0; j < g.length; j++) {
			var f = g[j]; 
			f && (n = f.split(/=/),
			s = n[0],
			l = n[1],
			h[s] === void(0) ? h[s] = l : ("object" != typeof h[s] && (h[s] = [h[s]]),
			h[s].push(l)))
		}			
	}
	return {
		           url : url,
		        toPage : p.hrefNoHash + "#" + i.pathname,
		      pageData : _.isEmpty(h) ? null : h,
		isEmbeddedPage : $.mobile.path.isEmbeddedPage(p)
	}
}
/* http://stackoverflow.com/questions/8366733/external-template-in-underscore
   usage: var someHtml = _.templateFromUrl("http://example.com/template.html", {"var": "value"});
**/
_.mixin({templateFromUrl: function (url, data) {
    this.cache = this.cache || {};

    if (!this.cache[url]) {
        var t = ""; 
        $.ajax({
            url: url+uniqId("?"),
            method: "GET",
            async: false,
            success: function(res) {
                t = res;
            }
        });
        this.cache[url] = _.template(t);
    }

    return this.cache[url](data);
}});
/* utils functions
**/
function isMobile() {return /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(window.navigator.userAgent.toLowerCase());}
function vfTackEvent(action,label) { try{_gaq.push(['_trackEvent', _usr, action,label ], 1);} catch(err) {}	}  //_trackEvent(category (:_usr), action (:action), opt_label (:etichetta), opt_value, opt_noninteraction)
function v2s(s,i)  {if(s===void(0))return ''; return (i ? i+'="'+s+'"':s)}
function uniqId(p) {return v2s(p)+Math.round(new Date().getTime() + (Math.random() * 100));} // better then _.uniqId
function defaultFor(arg, val) { return typeof arg !== 'undefined' ? arg : val; }
function hash(s) {s+="";var h=0,i,c;if(s.length===0)return h;for(i=0;i<s.length;i++){c=s.charCodeAt(i);h=((h<<5)-h)+c;h|=0;} return h;} //https://stackoverflow.com/questions/7616461/generate-a-hash-from-string-in-javascript-jquery
function voidif(v,s) {if (v===defaultFor(s,'')) return void(0); return v;} function empty(v)  {if(typeof(v)==='undefined'||v===null||v===''||v===false||v===0||v==='0') return true;return false } 
function guid() {return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) { var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8); return v.toString(16); })}
function orderId(c) {var s=(c||'').toString(),d=new Date().getTime().toString(32); return (s.length>0? s+'.'+d:d);}  //266006.5c61a421
//function vfWaitOn()  {setTimeout(function() {$.mobile.loading("show",{text:"Attendere prego...",textVisible:true,theme:"a",textonly: false,html:""})},1);} 
function vfjqxKoPop(j,t,e) { var r=j.responseText,i=r.indexOf("{");vfTackEvent('error jqXHR',r); vfPopUp("errore", i<0 ? r : r.substr(0,i)); }
function vfRspOkPop(r,btnOK){r=r||{};vfPopUp(r.Result ||'OK', (r.Message||r.message||_working)+'\n'+(btnOK||'')); }
function vfRspKoPop(r,btnKO){r=r||{};vfPopUp(r.Result ||_error, (r.Message||r.message||_server_error)+'\n'+(btnKO||''),btnKO===void(0)); }
function bfRspOK(rsp){ var r = rsp || {}; return r.status===true || r.Result ==='OK';}  //response from mirrow or page
function vfWaitOn()  {setTimeout(function() {$.mobile.loading("show");},1);} 
function vfWaitOff() {setTimeout(function() {$.mobile.loading("hide");},300);} 
function getGet(a){for(var b=window.location.search.substring(1),c=b.split("&"),d=0;d<c.length;d++){var e=c[d].split("=");if(e[0]==a)return e[1]}}
function getBiskuit(a){for(var b=a+"=",c=document.cookie.split(";"),d=0;d<c.length;d++){var e=c[d].trim();if(0==e.indexOf(b))return e.substring(b.length,e.length)}return""}
function vfWait(showOrHide, delay) { //http://www.gajotres.net/show-loader-on-ajax-call-in-jquery-mobile/
  setTimeout(function() { 
    $.mobile.loading(showOrHide);
  }, delay);
}
/*
 reconize ajax with plus variable ajax:on
 o.action, r.l, r.a, r.xxx   [{name:action,value:xyz}]  || {action:xyz}
     beforeSend : function(xhrObj){
                    xhrObj.setRequestHeader("Content-Type","application/json");
                    xhrObj.setRequestHeader("Accept","application/json");
                    vfWaitOn();
                 },
                 
 flat.   $(this).serializeArray().reduce(function(m,o){ m[o.name] = o.value; return m;}, {})
 _.reduce($(this).serializeArray(),function(a,f){a[f.name]=f.value;return a;},{});
 JSON.stringify(_.isArray(o)?_.reduce(o,function(a,f){a[f.name]=f.value;return a;},{}):o)
 if(!_.any(o, function(v, k) {return k==='action' || v.name==='action'}))return;  // nothing or no action 
 **/
function sfVal4Serialized(fsa,v) { //fsa=form serialized array
	var r=_.find(fsa, function(o, k) {return k === v || o.name === v});
	return _.isObject(r) ? r.value : r;
}	
function vfActionLoad(d,vfCallBack) {
	d = d||{};
	var aa = sfVal4Serialized(d,'action'); 
    if(!aa) return vfPopUp(_error,_unrecognized);
    
    vfTackEvent( 'action:'+ aa );

    $.ajax({
		  data : d, 
		   url : d._url || $.mobile.path.getDocumentUrl(), //$.mobile.path.getDocumentBase(true).pathname,
          type : sfVal4Serialized(d,'_type') || sfVal4Serialized(d,'_method') ||'GET',                  
         async : 'true',
      dataType : 'json',
    beforeSend : vfWaitOn,
      complete : vfWaitOff,
       success : function(r) { vfCallBack(aa,r); },
         error : vfjqxKoPop //jqXHR, textStatus, errorThrown
    }); // ajax      
}

/*
**/
function vfPopUp(header,body,bclose,callBack,ttime) {
    var s,h=header||'',b=body||'',f=b.indexOf('<form')>-1,i=uniqId('j');
	
	h = h.replace(/<\/?[^>]+(>|$)/g, '');                 
	if(!f) b = b.replace(/<\/?[^>]+(>|$)/g, ''); // no form? but html
	b = b.replace(/\n/g,'<br>')			                                 //h:href, i:icon, t:text. m:more 
		 .replace(/\{\{(.*?):(.*?):(.*?):(.*?)\}\}/g,function(s,h,i,t,m){ return sfjqmButton(h,i,t,m); });
    vfWaitOff(); // eventually....

    s = '<div data-role="popup" id="'+ i +'" data-theme="a" class="ui-corner-all" style="/* min-width:300px;*/"><div data-role="header"><h2>' + h + '</h2>';
	if(bclose) s += '<a href="#" data-rel="back" data-role="button" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>';
	s += '</div><div data-role="content" class="ui-content" style="'+ (f?'':'text-align:center;') +'"><p >'
	if(h.toLowerCase().indexOf('error')!=-1) s += '<p style="color:red">';
	else                                     s += '<p>';                                     
	s += b +'</p></div></div>';
   
    $(s).appendTo( $.mobile.activePage ).popup().popup("open").trigger('create').on("popupafterclose", function () { $(this).remove();  });

    if(ttime) setTimeout(function(){$("#"+i ).popup("close");},ttime);  //autoclose
    //$("div:jqmData(role=popup)").each(function(i){if($(this).parent().hasClass('ui-popup-hidden')) $(this).remove();});
              
    if(_.isFunction(callBack)) callBack(i);         
              
}
/* generic popup title,message,button goto
**/
function vfPopUpOK(hh1,ttext,hhref) { 
  var tt = 4000;
    if(!ttext.match(/\{\{.+?\}\}/)) {
		ttext += '\n{{'+hhref+':check:'+_ok+':}}';
		tt = 0;
	}
	vfPopUp (hh1 //header
			,ttext  // sfjqmButton(hhref,"check", "Ok")   //body
			,false   //bclose
			,void(0) //callback
			,tt      // time auto-close
	);
}
function vfPopUpDeleteConfirm(h,callBack) {
    // blue list selected + pop confirmation = delete + reload list || cancel + return list
    var s  = _confirm_delete +'\n';
        s += '{{:check yesdelete:'+_yes+':}}';   //sfjqmButton(idPagList,"check yesdelete","Si, cancella");						// href,css,text,more
        s += '{{:back:'+_no+':}}';               //sfjqmButton(idPagForm,"back"           ,"Ignora");
    
    vfPopUp (_warning		   //head
			,s				   //body
			,false  		   //bclose
			,function(popId){  //callBack
				$(".yesdelete").click(function() {
					$("#"+popId).popup('close');
					if(_.isFunction(callBack)) {
					    h.action = '_delete';
					    vfActionLoad(h,callBack);
					}
					return false;
				});	
    });   
}
function vfToolTip(i,body) {

    var j = uniqId('j'),p='#'+j, h='#'+i
    ,popup = '<div data-role="popup" id="'+ j +'" class="ui-content" data-theme="a" style="max-width:250px;">'+ body +'</div>';
/*
    ,popup = '<div data-role="popup" id="'+ j +'" class="ui-content" data-theme="a" style="max-width:350px;">
          + '<a href="#" data-rel="back" data-role="button" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>'
          + '<div data-role="content" class="ui-content" style="text-align:center;">'+ body +'</div></div>';
          
<div id="pre-rendered-screen" class="ui-popup-screen ui-screen-hidden"></div>
    <div id="pre-rendered-popup" class="ui-popup-container fade ui-popup-hidden ui-body-inherit ui-overlay-shadow ui-corner-all">
    <div id="pre-rendered" class="ui-popup" data-role="popup" data-enhanced="true" data-transition="fade">
        <p>This is the contents of the pre-rendered popup</p>
    </div>
</div>
*/
    $(popup).appendTo( $.mobile.activePage )
            .popup({
                'positionTo': h,
                'afterclose':function(){$(this).remove()}
            })
            .trigger('create');   //.enhanceWithin()
          
               
    setTimeout(function(){ $(p).popup('open');  }, 100);
    setTimeout(function(){ if($(p).length) $(p).popup('close'); }, 3500); //close after 4 sec
}

/* ---- crud section
**/
/* response status error
   1. simple: popup with error
   2. page form:need page (page_ide) 
   3. popup form: check .ui-popup form
   @ p page_id
   @ r response json
   vfActionErrorOk(r,idPagForm,"Modifica" ,idPagForm);
**/ 
function vfActionErrorOk(r,page_id,h,gto,msg) {
  var i,e,n,f,b=0;
    
    if ( $(".ui-popup form:not(.ui-filterable)").length )     f = ".ui-popup form";  // there is a pop with a form                                             
    else if	( $(page_id+" form:not(.ui-filterable)").length ) f = page_id+" form";   // there is a page with a form
    
    if(f) {                                                      // if there is a form
        $(f+" .error").remove();                                 // remove old errors in form
        if(r && r.l) for (n in r.l) {                            // print new errors in form
            if(_.isObject(r.l[n]) && !empty(r.l[n].err)) {       // after or before input fields
                b++;                                             // count errors
                e = $( "<span>" ).addClass( "error" ).html( r.l[n].err );
                switch (r.l[n].t) {
                /****/ case 'mselect'  : 
                       case 'select'   :
                       case 'flip'     : i = $(f+" select[name='"+n+"[]']");                 e.insertAfter(i);
                break; case 'checkbox' : 
                       case 'radio'    : i = $(f+" input[name='"+n+"[]']").first().parent(); e.insertBefore(i);
                break; default         : i = $(f+" input[name='"+n+"']");                    e.insertAfter(i);
                } // switch
            }
        } // for 
    }
    if(b>0) { //if errors
        n = r.message.replace(/<\/?[^>]+(>|$)/g, "");  // about server error with garbage html in, or $('<div>').html( r.message).text()
        //n = $('<div>').html( r.message).text();
        if ( $(".ui-popup form").length )  $('<span class="error">'+n+'</span>').appendTo($(".ui-popup form"));  // form error in form
        else /* ---------------------- */  vfPopUp('error',r.message);                                           // form error popuped   
    } else vfPopUpOK(h,msg||r.message,gto);
    
    // remove hidden popup covered by this!!
    $("div:jqmData(role=popup)").each(function(i){if($(this).parent().hasClass('ui-popup-hidden')) $(this).remove();})
}
/* crud field
**/
function sfFormField(f) {
    var sfue       = s => _.escape(_.unescape(_.replace(s,/&#039;/g, '&#39;')));   // lodash escape \' in &#39; php::json_encode  &#039;
    var isSelected = function(v,k) { if ( (_.isArray(v) && _.includes(v,k)) || (!_.isArray(v) && v==k) ) return "selected"; return ""; }
    var isChecked  = function(v,k) { if ( (_.isArray(v) && _.includes(v,k)) || (!_.isArray(v) && v==k) ) return "checked"; return ""; }
    var sfButton1  = function(v,i) { return '<label> </label><div class="ui-grid-a"><div class="ui-block-a"><input type="submit" value="'+v+'" data-icon="'+i+'"></div>'
                                        + '<div class="ui-block-b"><a href="#" class="ui-btn ui-icon-back ui-btn-icon-left" data-rel="back">'+_cancel+'</a></div></div>';
                   }  
    var sfButton   = function(v,i) { return '<label> </label><input type="submit" value="'+v+'" data-icon="'+i+'">';  }     
    var sfHelp     = function(i,h) { return '&nbsp;<a href="#" id="help_'+ i +'" style="background:none;border:0;" class="ui-btn ui-btn-a ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"'
                                        +' onclick="vfToolTip(\'help_'+ i +'\',\''+h+'\');" title="Help">Help</a>';
                   };
     
                 

    var  i = f.name || f.n || f.i //uniqId('i')
        ,n = f.name || f.n
        ,t = f.type || f.t || 'hidden'
        ,c = f.c // class
        ,l = sfue(f.label || f.l ) + (f.a ? '<em>&nbsp;*</em>' : '')  +(!empty(f.h) ? sfHelp(i,sfue(f.h)) : '')
        ,vv= f.value || f.v // || f.d  // no default: do it from php  value=0 go to default : nogood
        ,v = _.isArray(vv) ? _.map(vv ,function(e) {return sfue(e);}) : sfue(vv)
        ,p = sfue(f.placeholder || f.p)
        ,e = sfue(f.error || f.err || f.e) 
        ,o = f.o || []  //options
        ,r = f.a ? 'requested' : ''
        ,s,w='',d='',j=0;
		
    switch(t) {
    /****/ case 'hidden':   s = '<input type="hidden" name="'+n+'" value="'+v+'">';
    break; case 'disabled': t = 'text'; r = 'disabled'; //forcing instead requested eventually
    /****/ case 'text':     
    /****/ case 'email':    
    /****/ case 'password': 
                            s = '<label for="'+i+'">'+l+'</label>';
							//s+= '<input type="'+t+'" name="'+n+'_fakename" style="display:none;">'
							s+= '<input type="'+t+'" id="'+i+'" name="'+n+'" value="'+v+'" placeholder="'+p+'" '+r+' autocomplete="new-'+t+'">';
	break; case 'text_dl':  // http://demo.agektmr.com/datalist/
							s = '<label for="'+i+'">'+l+'</label>';   
							s+= '<input type="text" id="'+i+'" name="'+n+'" value="'+v+'" placeholder="'+p+'" '+r+' list="'+i+'-list">';
							s+= '<datalist id="'+i+'-list">';
							for (k in o) s += '<option>'+_.escape(o[k])+'</option>';
							s+= '</datalist>';
	
    break; case 'submit':   s = '<input type="submit" name="'+n+'" value="'+v+'" data-inline="true">';              
    break; case 'button':   s = '<button class="'+c+'" id="'+i+'">'+v+'</button>';
    break; case 'anchor':   s = '<a href="#" class="'+c+'" id="'+i+'">'+v+'</a>';
      
    break; case 'insert':   s = sfButton(_insert,'check' );         // submit buttons widh icon
    break; case 'update':   s = sfButton(_update,'edit'  ); 
	break; case 'delete':   s = sfButton(_delete,'delete');   
    break; case 'copy':     s = sfButton(_copy  ,'action');      
                            
    break; case 'checklist':
                            s = '<fieldset data-role="controlgroup"><legend>'+l+'</legend><ul class="'+t+'">';
                            for (k in o) { 
                                s += '<li><a href="#" class="ui-btn ui-btn-icon-left ui-icon-'+ (isChecked(v,k)=='' ? 'delete' : 'check') +'">'+_.escape(o[k])+'</a></li>';
                            }
                            s += '</ul></fieldset>';
    
    break; case 'checkbox': n += '[]'; //name[] for multiple
           case 'radio':        
                            s = '<fieldset data-role="controlgroup"><legend>'+l+'</legend>';
                            
                            for (k in o) { // AAA k is string, in  isChecked(v,k) = v array of string
                                s += '<input '+d+' type="'+t+'" id="'+i+'-'+j+'" name="'+n+'" value="'+_.escape(k)+'" '+isChecked(v,k)+'>';
                                s += '<label for="'+i+'-'+j+'">'+_.escape(o[k])+'</label>';
                                j++;
                            }
                            s += '</fieldset>';
                            
    break; case 'mselect':  n += '[]'; //name[] for multiple
                            if (w==='') w = 'data-native-menu="false" multiple="multiple" data-overlay-theme="b"';

           case 'select':   if (w==='') w = 'data-native-menu="false" data-overlay-theme="b"';
           case 'flip':     if (w==='') w = 'data-role="flipswitch"';
                            s = '<label for="'+i+'">'+l+'</label><select id="'+i+'" name="'+n+'" '+w+'>';  // data-native-menu="false"
                            if(!empty(p) && t!='flip') s += '<option value="" data-placeholder="true">'+p+'</option>';
                            for (k in o) // AAA k is string, in  isSelected(v,k) = v array of string
                                s += '<option value="'+_.escape(k)+'" '+isSelected(v,k)+'>'+_.escape(o[k])+'</option>';				
                            s += '</select>'; 
                            if(!empty(p) && t=='flip') s += '&nbsp;<span> '+p+'</span>';
    break; case 'photo':
                            s = '<label for="'+i+'">'+l+'</label>';
                            s += _.templateFromUrl("tmpl/image-cropper-form2.html", {key:i,name:n});
                            //s += _.template($("#tmpl-image-cropper").html())({key:i,name:n});  // form
                            
                            if($( '#'+i ).length > 0) $( '#'+i ).remove();  //recreate every form view (new images...)
                            
                           //w =  _.templateFromUrl("tmpl/image-cropper-panel.html",{key:i,o:o}); // panel
                           //$.mobile.pageContainer.append( w );  // to create dinamic panel: https://jqmtricks.wordpress.com/2014/04/13/dynamic-panels/
                           //$( '#'+i ).panel().enhanceWithin();
                               
    break; case 'fake':     s = '<input style="display:none" type="text" value="fake" name="fakeusernameremembered"/>';
							s += '<input style="display:none" type="password" value="fake" name="fakepasswordremembered"/>';
    }//switch
    
    if(!_.includes(['hidden','fake'],t))
                            //s = '<div class="ui-field-contain">'+s+'<span id="'+i+'-err" class="error">'+e+'</span></div>';
                            s = '<div class="ui-field-contain">'+s+'</div>';
    
    return s;
}
/*
**/
function sfjqmButton(hhref,iicon,ttext,mmore) {  
    return '<a href="'+hhref+'" '
			+(mmore?mmore:'')
			+(iicon==='back'?' data-rel="back"':'')
			+' class="ui-btn ui-btn-a ui-btn-inline'+(iicon?' ui-icon-'+iicon:'')+' ui-btn-icon-left ui-shadow ui-corner-all" >'
			+ttext+'</a>';
}
// photo field export
function vfPhotoFieldExport() {
    if ( $('.cropit-image-data').length > 0 ) { // check if cropit is in form and get value
         var imageData = $('#image-cropper').cropit('export');
         $('.cropit-image-data').val(imageData);
         //console.log ("imageData="+imageData.length);
    }
}
// to enhance photo field: only one in page 
function vfPhotoFieldEnhance(r,w,h) {
    if (!r.status) return;
    var n,v;
    for (n in r.l) { // there is a photo field?
        if (r.l[n].t==='photo') {
            v =  _.escape(r.l[n].value || r.l[n].v); 
 
            $('#image-cropper').cropit({                    // photo cropit enhancer.... only if you have photo!
                imageState: { src: v  }        
                ,smallImage: 'stretch'
                ,width: w?w:80
                ,height:h?h:80
                ,minZoom:'fit'
                ,maxZoom: 2
                //,freeMove:true
            });
            $('.cropit-image-input').parent().hide();       // annoing problem on markup buid by jquery mobile over hidden input
            $('.select-image-btn').click(function() {       // play attention to header [data-tap-toggle="false"] otherwise range-zoom not working
                $('.cropit-image-input').click();
                $('.cropit-image-zoom-input').slider( "enable" );
            });        
            $('.img2cropit').on('click',function(e) {               
                $('#image-cropper').cropit('imageSrc', this.src);   // images loaded to cropit
                $('.ui-panel.ui-panel-open').panel( 'close' );      // close the panel where images loaded
                $('.cropit-image-zoom-input').slider( "enable" );   // some times happen is disable
            });
            $('.rotate-cw-btn').click(function() {                  // Handle rotation
                $('#image-cropper').cropit('rotateCW');
            });
            $('.rotate-ccw-btn').click(function() {
                $('#image-cropper').cropit('rotateCCW');
            });
        
            break;                                                  // one photo per form
            
        }
    }
}
/*  r = {}, a = ""
**/
function sfBuidForm(r,c) {
	if (r.status) {
		var h=[],n,s;

		for (n in r.l) {
			r.l[n].name = n;            
			h.push( sfFormField( r.l[n]) );
		}
		h.push( sfFormField({n:'action' ,v:r.action}) ); //hidden default
		h.push( sfFormField({n:'_method',v:'POST' }) ); //hidden method
		if(c) h.push( sfFormField({t:c}) ); // tasto finale
		return '<form method="post" autocomplete="off">'+ h.join("") +'</form>';
		
	}
	else return '<h1>'+_error+'</h1><p>'+ r.message + '</p>';
}
//http://api.jquerymobile.com/popup/
function ofPopScale( width, height, padding, border ) {
    var scrWidth = $( window ).width() - 30,
        scrHeight = $( window ).height() - 30,
        ifrPadding = 2 * padding,
        ifrBorder = 2 * border,
        ifrWidth = width + ifrPadding + ifrBorder,
        ifrHeight = height + ifrPadding + ifrBorder,
        h, w;
 
    if ( ifrWidth < scrWidth && ifrHeight < scrHeight ) {
        w = ifrWidth;
        h = ifrHeight;
    } else if ( ( ifrWidth / scrWidth ) > ( ifrHeight / scrHeight ) ) {
        w = scrWidth;
        h = ( scrWidth / ifrWidth ) * ifrHeight;
    } else {
        h = scrHeight;
        w = ( scrHeight / ifrHeight ) * ifrWidth;
    }
 
    return {
        'width': w - ( ifrPadding + ifrBorder ),
        'height': h - ( ifrPadding + ifrBorder )
    };
};
/* ---- end crud section
**/


