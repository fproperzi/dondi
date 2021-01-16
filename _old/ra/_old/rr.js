/*
 reconize ajax with plus variable ajax:on
 o.action, r.l, r.a, r.xxx
 **/
function vfActionLoad(o,vfCallBack) {
    o = _.extend(o || {},{'ajax':'on'}); // is ajax call please give/respond json: 'ajax':'on' send by default
    $.ajax({
     beforeSend: function(xhrObj){
                    xhrObj.setRequestHeader("Content-Type","application/json");
                    xhrObj.setRequestHeader("Accept","application/json");
                 },
           url : o.url || $.mobile.path.getDocumentUrl(), //$.mobile.path.getDocumentBase(true).pathname,
          data : o,  // _.extend(o || {},{action:a}), //_.extend(f,{action:action}),		//$form.serialize(),
          type : 'POST',                  
         async : 'true',
      dataType : 'json',
    beforeSend : function()  { $.mobile.loading( "show" ); },
      complete : function()  { $.mobile.loading( "hide" ); },
       success : function(r) { vfCallBack(o.action,r); },
         error : function(j,t,e)  { 
                    vfCallBack(o.action,{"status":false,l:[],"message":"{{err.server}}"+j.responseText.substr(0,j.responseText.indexOf("{"))  }); 
                 } //jqXHR, textStatus, errorThrown
    }); // ajax      
}
/* http://stackoverflow.com/questions/8366733/external-template-in-underscore
   usage: var someHtml = _.templateFromUrl("http://example.com/template.html", {"var": "value"});
**/
_.mixin({templateFromUrl: function (url, data, settings) {
    var templateHtml = "";
    this.cache = this.cache || {};

    if (this.cache[url]) {
        templateHtml = this.cache[url];
    } else {
        $.ajax({
            url: url,
            method: "GET",
            async: false,
            success: function(data) {
                templateHtml = data;
            }
        });

        this.cache[url] = templateHtml;
    }

    return _.template(templateHtml, data, settings);
}});
// is mobile return true/false
var isMobile = function() {
	var check = false;
	(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
	return check; 
}
/* utils functions
**/
function v2s(s,i)  {if(s===void(0))return ''; return (i ? i+'="'+s+'"':s)}
function uniqId(p) {return v2s(p)+Math.round(new Date().getTime() + (Math.random() * 100));} // better then _.uniqId
function defaultFor(arg, val) { return typeof arg !== 'undefined' ? arg : val; }
function voidif(v,s) {if (v===defaultFor(s,'')) return void(0); return v;}
function empty(v)  {if(typeof(v)==='undefined'||v===null||v===''||v===false||v===0||v==='0') return true;return false }

/* crud field
**/
function sfFormField(f) {

    var isSelected = function(v,k) { if ( (_.isArray(v) && _.includes(v,k)) || (!_.isArray(v) && v==k) ) return "selected"; return ""; }
    var isChecked  = function(v,k) { if ( (_.isArray(v) && _.includes(v,k)) || (!_.isArray(v) && v==k) ) return "checked"; return ""; }
    var sfButton   = function(v,i) { return '<label> </label><div class="ui-grid-a"><div class="ui-block-a"><input type="submit" value="'+v+'" data-icon="'+i+'"></div>'
                                        + '<div class="ui-block-b"><a href="#" class="ui-btn ui-icon-back ui-btn-icon-left" data-rel="back">Cancel</a></div></div>';
                   }                                                                                             
    var sfHelp     = function(i,h) { return '&nbsp;<a href="#'+i+'-help" data-rel="popup" data-transition="pop" class="tooltip-btn ui-btn ui-btn-b ui-nodisc-icon ui-corner-all ui-icon-info ui-btn-icon-notext ui-btn-inline" title="Help">Help</a>'
                                        + '<div data-role="popup" id="'+i+'-help" class="ui-content" data-theme="a" style="max-width:350px;">'+h+'</div>';
                   }
    var afi18nOpt  = function(o) { if(!o)return; var a=[],k;for (k in o) a[k]=$.i18n._(o[k]);return a; }
    var  i = f.name || f.n || f.i //uniqId('i')
        ,n = f.name || f.n
        ,t = f.type || f.t || 'hidden'
        ,c = f.c // class
        ,l = _.escape( $.i18n._(f.label || f.l) ) + (f.a ? '<em> *</em>' : '') +(!empty(f.h) ? sfHelp(i, $.i18n._(f.h) ) : '')
        ,p = _.escape( $.i18n._(f.placeholder || f.p) )
        ,e = _.escape( $.i18n._(f.error || f.err || f.e) ) 
        ,v = _.isArray(f.value || f.v) ? _.map(f.value || f.v ,function(e) {return _.escape(e);}) : _.escape(f.value || f.v)
        ,o = afi18nOpt(f._o) || f.o || []  //options if _o pass thru getext
        ,r = f.a ? 'requested' : ''
        ,s,w='',d='',j=0;
		
    switch(t) {
    /****/ case 'hidden':   s = '<input type="hidden" name="'+n+'" value="'+v+'">';
    break; case 'text':     
    /****/ case 'email':    
    /****/ case 'password': 
                            s = '<label for="'+i+'">'+l+'</label>';
							//s+= '<input type="'+t+'" name="'+n+'_fakename" style="display:none;">'
							s+= '<input type="'+t+'" id="'+i+'" name="'+n+'" value="'+v+'" placeholder="'+p+'" '+r+' autocomplete="off">';
	break; case 'text_dl':  // http://demo.agektmr.com/datalist/
							s = '<label for="'+i+'">'+l+'</label>';   
							s+= '<input type="text" id="'+i+'" name="'+n+'" value="'+v+'" placeholder="'+p+'" '+r+' list="'+i+'-list">';
							s+= '<datalist id="'+i+'-list">';
							for (k in o) s += '<option>'+_.escape(o[k])+'</option>';
							s+= '</datalist>';
	
    break; case 'submit':   s = '<input type="submit" name="'+n+'" value="'+v+'" data-inline="true">';              
    break; case 'button':   s = '<button class="'+c+'" id="'+i+'">'+v+'</button>';
    break; case 'anchor':   s = '<a href="#" class="'+c+'" id="'+i+'">'+v+'</a>';
      
    break; case 'insert':   s = sfButton('New','plus');         // submit buttons: value+icon
    break; case 'update':   s = sfButton('Update','edit'); 
	break; case 'delete':   s = sfButton('Delete','delete');   
    break; case 'copy':     s = sfButton('Copy','camera');      
                            
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
                            s += _.template($("#tmpl-image-cropper").html())({key:i,name:n});  // form
                            
                            if($( '#'+i ).length > 0) $( '#'+i ).remove();  //recreate every form view (new images...)
                            
                            w = _.template($("#tmpl-image-cropper-panel").html())({key:i,o:o}); // panel
                            $.mobile.pageContainer.append( w );  // to create dinamic panel: https://jqmtricks.wordpress.com/2014/04/13/dynamic-panels/
                            $( '#'+i ).panel().enhanceWithin();
                               
    break; case 'fake':     s = '<input style="display:none" type="text" name="fakeusernameremembered"/>';
							s += '<input style="display:none" type="password" name="fakepasswordremembered"/>';
    }//switch
    
    if(!_.includes(['hidden','fake'],t))
                            //s = '<div class="ui-field-contain">'+s+'<span id="'+i+'-err" class="error">'+e+'</span></div>';
                            s = '<div class="ui-field-contain">'+s+'</div>';
    
    return s;
}
/*  r = {}, a = ""
**/
function sfBuidForm(r,a) {
	if (r.status) { // you are here because status == true! this is a bonus
		var h=[],n;

		for (n in r.l) {
			r.l[n].name = n;            
			h.push( sfFormField( r.l[n]) );
		}
		h.push( sfFormField({n:'action',v:r.action}) ); // hidden default
		h.push( sfFormField({t:a}) );					// buttons per action
	    return '<form method="post">'+ h.join("") +'</form>';
		
	}
	else return "<h1>"+$.i18n._('err.error')+"</h1><p>"+ $.i18n._(r.message) + "</p>";
}

/* response status error
   1. simple: popup with error
   2. page form:need page (page_ide) 
   3. popup form: check .ui-popup form
   @ p page_id
   @ r response json
**/
function vfResponseErrorView(r,page_id) {
  var i,e,n,f;
    
	if(page_id===void(0)) page_id = "#"+$.mobile.pageContainer.pagecontainer( 'getActivePage' ).attr( 'id' ); 
	
    if ( $(".ui-popup form").length )     f = ".ui-popup form";  // there is a pop with a form                                             
    else if	( $(page_id+" form").length ) f = page_id+" form";   // there is a page with a form
    
    if(f) {                                                      // if there is a form
        $(f+" .error").remove();                                 // remove old errors in form
        for (n in r.l) {                                         // print new errors in form
            if(!empty(r.l[n].err)) {                             // after or before input fields 
				e = $( "<span>" ).addClass( "error" ).html( _.escape($.i18n._(r.l[n].err)) );
                switch (r.l[n].t) {
                /****/ case 'mselect'  : 
                       case 'select'   :
                       case 'flip'     : i = $(f+" select[name='"+n+"[]']");                 e.insertAfter(i);
                break; case 'checkbox' : 
                       case 'radio'    : i = $(f+" input[name='"+n+"[]']").first().parent(); e.insertBefore(i);
                                         //i = $("input[name='"+n+"[]']").last().parent(); e.insertAfter(i);
                break; default         : i = $(f+" input[name='"+n+"']");                    e.insertAfter(i);
                } // switch
            }
        } // for
    }
 
    //n = r.message.replace(/<\/?[^>]+(>|$)/g, "");  // about server error with garbage html in, or $('<div>').html( r.message).text()
    //n = $('<div>').html( r.message).text(); 
    if ( $(".ui-popup form").length )  $('<span class="error">'+ _.escape($.i18n._(r.message)) +'</span>').appendTo($(".ui-popup form")); // form error in form
    else                               vfPopUpErr( 'err.error',r.message );           // form error popuped   
}

/* generic button inline
**/
function sfjqmButton(hhref,iicon,ttext,mmore) {  
    return '<a href="'+hhref+'" '+mmore+' class="ui-btn ui-btn-b ui-btn-inline ui-icon-'+iicon+' ui-btn-icon-left ui-shadow ui-corner-all" >'+ttext+'</a>';
}
/* generic popup title,message,button
**/
function vfPopUpOK(hh1,ttext,hhref) { 
	vfPopUp( $.i18n._(hh1)
		,'<h3>'+ $.i18n._(ttext) +'</h3><br/>'
		+ sfjqmButton(hhref,"check", $.i18n._('crud.ok') )
	);
}
/* generic popup error title,message,button
**/
function vfPopUpErr(hh1,ttext,hhref) { 
	if(empty(hhref)) hhref="#";
	vfPopUp( $.i18n._(hh1)
		,'<h3 class="error">'+ _.escape($.i18n._(ttext)) 
		+'</h3><br/><div align="center">'
		+ sfjqmButton(hhref,"alert", $.i18n._('crud.ok') ,'data-rel="back"')
		+'</div>'
	);
}
// to enhance photo field
function vfPhotoFieldCropitEnhance(v) {
   
    $('#image-cropper').cropit({                    // photo cropit enhancer.... only if you have photo!
        imageState: { src: v + uniqId("?") }        // avoid cache
        ,smallImage: 'stretch'
        ,width:80
        ,height:80
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
    
}
/*jqm popup and callback
**/
function vfPopUp(header,body,callBack) {
  
    var i = uniqId('i')
       ,popup = '<div data-role="popup" id="'+ i +'" data-theme="a" class="ui-corner-all" style="min-width:300px;max-width:400px;">'
              + '<div data-role="header" data-theme="b"><h2>' + header + '</h2>' 
              + '<a href="#" data-rel="back" data-role="button" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>'
              + '</div><div data-role="content" class="ui-content"><p>'+ body +'</p></div></div>';
   
    $( popup ).appendTo( $.mobile.activePage )
              .popup().popup("open")
              .trigger('create')   //.enhanceWithin()
              .on("popupafterclose", function () { 
                $(this).remove(); 
              });
              
    if(callBack && typeof callBack === "function") callBack(i);         
              
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

/* to drag and drop  
   ul = '#page-list-approved-ul'    ... remember '#'  !!!  
   fld_order = "lkp_order"  text
   vfCallBack = vfActionSwitch  function
**/
function vfSlipInit(ul,fld_order,vfCallBack) {
    Slip(ul);
    
    $(ul)[0].addEventListener('slip:beforewait', function(e){
        if (e.target.classList.contains('slip') ) 
            e.preventDefault();
    }, false);
    $(ul)[0].addEventListener('slip:reorder', function(e){
        e.target.parentNode.insertBefore(e.target, e.detail.insertBefore);
        
        $(e.target).jqmData(fld_order,e.detail.spliceIndex+1);
        vfActionLoad( $(e.target).data(),vfCallBack );
        return false;
    }, false);
    
}

/**
* i18n provides a mechanism for translating strings using a jscript dictionary.
* mix from:
* https://github.com/recurser/jquery-i18n/blob/master/jquery.i18n.js
* https://github.com/flyingzl/jqm-i18n
*/
(function($,window,document,undefined) {
    var __slice = Array.prototype.slice;
    var k2t = "data-i18n",       //key to text   data-i18n="tons"   -> "get %s tons of tomato"
        p4t = "data-i18n-param", //parameter for text   "12"   ->  "get 12 tons of tomato"
        dict = $.data(window,"i18n-l") || {}, I18n = {      // i18n property list
       
		getText: function( key ){
			if( !key || typeof( key ) !== "string" ) return;
			var parts = key.split("."),
				obj = dict;
            if ( typeof( obj ) !== "object" ) return;
			for( var i=0, p ; p = parts[i]; i++){
				obj =  ( p in obj ) ? obj[p] : undefined;
		        if ( obj === undefined ) return;
			}
	
			return obj;
		},
		applyI18n: function( ele ){
			var $eles = $( ele ),
				getText = this.getText;
			if( $eles.length ===0 ) return;

			var applyContext = function() {
				var inputs = "input,textarea,select",
					$this = $( this ),
					key = $this.attr( k2t ),
					value = getText( key ),
					reg = new RegExp( "\\b" + this.nodeName.toLowerCase() + "\\b" );
                if(typeof(value)!=='undefined') //fallback
                    inputs.match( reg ) ? $this.val( value ) : $this.text( value );
			};
			
			var apply2Ele = function( $ele ) {
				$ele.children().length === 0 ? 
					$ele.each( applyContext ) : 
					$ele.find( "["+ k2t +"]" ).each( applyContext );
			};

			return $eles.each( function(){
				var $ele = $(this),
					isScriptEle = $ele[0].tagName.toLowerCase() === "script",
					scriptType  = $ele[0].type,
					$div = $("<div></div>");
				if( scriptType === "" || scriptType === "text/javascript" ) return;
				apply2Ele( isScriptEle ? $div.html( $ele.html() ) : $ele );
				isScriptEle && $ele.html( $div.html() );
			});
		},
        /* get all i18n key-values on page
           $.i18n.geti18n();
        **/
        geti18n: function() {
            var a=[],b;
            $(document).find( "[data-i18n]").each(function(){
                var $this=$(this)
                    ,k=$this.jqmData('i18n')
                    ,v=$this.text() || $this.val();
                a[k] = v;
                //console.log('"'+k+'":"'+v+'"');
            });
            //sort by keys 
            b = Object.keys(a).sort().reduce(function (result, key) {
                result[key] = a[key];
                return result;
            }, {});
            console.log(JSON.stringify(b));
        },
        /** 
         * Substitutes %s with parameters given in list. %%s is used to escape %s.
         *
         * @param  string str    : String to perform printf on.
         * @param  string args   : Array of arguments for printf.
         * @return string result : Substituted string
         */
        printf: function(str, args) {
            if (arguments.length < 2) return str;
            args = $.isArray(args) ? args : __slice.call(arguments, 1);
            return str.replace(/([^%]|^)%(?:(\d+)\$)?s/g, function(p0, p, position) {
                if (position) {
                  return p + args[parseInt(position)-1];
                }
                return p + args.shift();
            }).replace(/%%s/g, '%s');
        },
        /**
         * _() is not underscore! ,-)  
         *
         * Looks the given string up in the dictionary and returns the translation if
         * one exists. If a translation is not found, returns the original word.
         * es.: $.i18n._('some %s text',12)  or   $.i18n._('sss',12)  where dict['sss']='some %s text'
         *      $.i18n._("{{sss}}"); is the same of $.i18n._("sss"); but 
         *
         * @param  string str           : The string to translate.
         * @param  property_list params.. : params for using printf() on the string.
         * @return string               : Translated word.
         */
        _: function (str) {
            if( !str || typeof( str ) !== "string" || str.length==0) return;
            args = __slice.call(arguments);
            var i,s,m=str.match(/{{.+?}}/g) || []; // "every {{_back}} is {{_yes}} indeed"
            if(m.length) {                         // if is a multiple lookup string 
                for(i=0;i<m.length;i++) {          // trasform every {{.+?}} with gettext
                    s = m[i].slice(2,-2);
                    s = this.getText(s) || s;
                    str=str.replace(m[i],s);
                }
                args[0] = str;
            }
            else 
                args[0] = this.getText(str) || str;
            
            // Substitute any params.
            return this.printf.apply(this, args);
        }
    } //var i18n = {
    

    /*
     * _t()
     *
     * Allows you to translate a jQuery selector.
     *
     * eg $('h1')._t('some %s text', 'foo')  
     *
     * @param  string str           : The string to translate .
     * @param  property_list params : Params for using printf() on the string.
     *
     * @return element              : Chained and translated element(s).
    */
    $.fn._t = function(str, params) {
        return $(this).html(I18n._.apply(I18n, arguments));
    };

    
    $.i18n = I18n;  //jquery obj   
    
    // change all data-i18n="xxx" values on page
    $(document).on("pagebeforecreate", function( evt ) { 
        var $page = $( evt.target );
        $.i18n.applyI18n( $().add( $page ).add( $page.find( "script" ) ) );  // script in page
	});

})(jQuery,window,document);
