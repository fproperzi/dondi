function sfFormField(f) {

    var isSelected = function(v,k) { if ( (_.isArray(v) && _.contains(v,k)) || (!_.isArray(v) && v==k) ) return "selected"; return ""; }
    var isChecked  = function(v,k) { if ( (_.isArray(v) && _.contains(v,k)) || (!_.isArray(v) && v==k) ) return "checked"; return ""; }
    var sfButton   = function(v,i) { return '<label> </label><div class="ui-grid-a"><div class="ui-block-a"><input type="submit" value="'+v+'" data-icon="'+i+'"></div>'
                                        + '<div class="ui-block-b"><a href="#" class="ui-btn ui-icon-back ui-btn-icon-left" data-rel="back">Cancel</a></div></div>';
                   }
    var sfHelp     = function(i,h) { return '<a href="#'+i+'-help" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="Help">Help</a>'
                                        + '<div data-role="popup" id="'+i+'-help" class="ui-content" data-theme="a" style="max-width:350px;">'+h+'</div>';
                   }

    var  i = f.name || f.n || f.i //uniqId('i')
        ,n = f.name || f.n
        ,t = f.type || f.t || 'hidden'
        ,c = f.c // class
        ,l = _.escape(f.label || f.l ) + (f.a ? '<em> *</em>' : '')  //+(!empty(f.h) ? sfHelp(i,f.h) : '')
        ,v = _.isArray(f.value || f.v) ? _.map(f.value || f.v ,function(e) {return _.escape(e);}) : _.escape(f.value || f.v)
        ,p = _.escape(f.placeholder || f.p)
        ,e = _.escape(f.error || f.err || f.e) 
        ,o = f.o || []
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
      
    break; case 'insert':   s = sfButton('New','plus');         // submit buttons 
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
    
    if(!_.contains(['hidden','fake'],t))
                            //s = '<div class="ui-field-contain">'+s+'<span id="'+i+'-err" class="error">'+e+'</span></div>';
                            s = '<div class="ui-field-contain">'+s+'</div>';
    
    return s;
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
    
    if ( $(".ui-popup form").length )     f = ".ui-popup form";  // there is a pop with a form                                             
    else if	( $(page_id+" form").length ) f = page_id+" form";   // there is a page with a form
    
    if(f) {                                                      // if there is a form
        $(f+" .error").remove();                                 // remove old errors in form
        for (n in r.l) {                                         // print new errors in form
            if(!empty(r.l[n].err)) {                             // after or before input fields
                e = $( "<span>" ).addClass( "error" ).html( r.l[n].err );
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
   
    n = r.message.replace(/<\/?[^>]+(>|$)/g, "");  // about server error with garbage html in, or $('<div>').html( r.message).text()
    //n = $('<div>').html( r.message).text();
    if ( $(".ui-popup form").length )  $('<span class="error">'+n+'</span>').appendTo($(".ui-popup form")); // form error in form
    else                               vfPopUp('Error','<h3 class="error">'+n+'<br/></h3><br/>');           // form error popuped   
}

/*
**/
function sfjqmButton(hhref,iicon,ttext,mmore) {  
    return '<a href="'+hhref+'" '+mmore+' class="ui-btn ui-btn-b ui-btn-inline ui-icon-'+iicon+' ui-btn-icon-left ui-shadow ui-corner-all" >'+ttext+'</a>';
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
/*  r = {}, a = ""
**/
function sfBuidForm(r,a) {
	if (r.status) {
		var h=[],n;

		for (n in r.l) {
			r.l[n].name = n;            
			h.push( sfFormField( r.l[n]) );
		}
		h.push( sfFormField({n:'action',v:r.action}) ); //hidden default
		h.push( sfFormField({t:a}) );
	    return '<form method="post">'+ h.join("") +'</form>';
		
	}
	else return "<h1>Error</h1><p>"+ r.message + "</p>";
}
// o.action, r.l, r.a, r.xxx
function vfActionLoad(o,vfCallBack) {
    
    $.ajax({
           url : $.mobile.path.getDocumentUrl(), //$.mobile.path.getDocumentBase(true).pathname,
          data :  o,  // _.extend(o || {},{action:a}), //_.extend(f,{action:action}),		//$form.serialize(),
          type : 'post',                  
         async : 'true',
      dataType : 'json',
    beforeSend : function()  { $.mobile.loading( "show" ); },
      complete : function()  { $.mobile.loading( "hide" ); },
       success : function(r) { vfCallBack(o.action,r); },
         error : function(j,t,e)  { 
                    vfCallBack(o.action,{"status":false,l:[],"message":"Server error: "+j.responseText.substr(0,j.responseText.indexOf("{"))  }); 
                 } //jqXHR, textStatus, errorThrown
    }); // ajax      
}
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
