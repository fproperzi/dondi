(function( $, window, undefined ) {
    /*  https://github.com/flyingzl/jqm-i18n
    **/
		
	var language =  window.navigator.language.replace("-","_"),
		defaultFolder = "i18n",
		mobile = $.mobile,
        version = $.mobile.version;
	
		
	mobile.i18n = {
		
		/**
		 * ???????
		 * @param {String} key ????????,?"user.name"
		 * @param {Object} context ????????,???window.i18n
		 * @method getText
		 */
		getText: function( key, context ){
			if( !key || typeof( key ) !== "string" ) return;
			var parts = key.split("."),
				obj = context || window.i18n || window;
			if ( typeof( obj ) !== "object" ) return;
			for( var i=0, p ; p = parts[i]; i++){
				obj =  ( p in obj ) ? obj[p] : undefined;
		        if ( obj === undefined ) return;
			}
	
			return obj;
		},
        getText_es: function( key, page ){
			if( !key || typeof( key ) !== "string" ) return;
			var obj = window.i18n || window;
			if ( typeof( obj ) !== "object" ) return;
            if ( page in obj )
                return obj[page][key] || obj[key]; //fallback on generic key, out of pages
            else
                return obj[key]; //try generic key
		},
		
		
		/**
		 * ????DOM?????????
		 * @param {DOM} 
		 * @return {jQuery} ????jQuery??
		 */
		applyI18n: function( ele ){
			var $eles = $( ele ),
				getText = this.getText;
			if( $eles.length ===0 ) return;

			var applyContext = function() {
				var inputs = "input,textarea,select",
					$this = $( this ),
					key = $this.attr( "data-i18n" ),
					value = getText( key ),
					reg = new RegExp( "\\b" + this.nodeName.toLowerCase() + "\\b" );
                if(typeof(value)!=='undefined') //fallback
                    inputs.match( reg ) ? $this.val( value ) : $this.text( value );
			};
			
			var apply2Ele = function( $ele ) {
				$ele.children().length === 0 ? 
					$ele.each( applyContext ) : 
					$ele.find( "[data-i18n]" ).each( applyContext );
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
		
        
         //$(document).find( "[data-i18n]").each(function(){console.log($( this ).jqmData('i18n'))})
		/**
		 * ??????????
		 * @return {String} ????????,?"zh-CN"?"en-US"?
		 */
		getLanguage: function() {
			return language.replace("_","-");
		},
        geti18n: function() {
            // get all i18n key-values on page
            // $.mobile.i18n.geti18n();
            var a=[],b;
            $(document).find( "[data-i18n]").each(function(){
                var $this=$(this)
                    ,k=$this.jqmData('i18n')
                    ,v=$this.text() || $this.val();
                a[k] = v;
                //console.log('"'+k+'":"'+v+'"');
            });
            
            b = Object.keys(a).sort().reduce(function (result, key) {
                result[key] = a[key];
                return result;
            }, {});
            console.log(JSON.stringify(b));
        }
		
	};
	
	
	var loadJSON = function( folder ) { 
		var url = folder + "/" + language + ".json?_=" + new Date().getTime();
		$.ajax({
			url: url,
			async: false,
			dataType: "json",
            scriptCharset: "utf-8",
            //contentType: "application/json; charset=utf-8",
			success: function( msg ){
				window.i18n = msg;
			},
			error: function(){
				console.error("error: Could not find file " +  url )
			}
		});
		
	}, i18n = mobile.i18n;
		
	
	// ?????????,?????$(document).bind("mobileinit",function(){})?????
	//if( mobile.i18nEnabled ) {
		/*
		var path = $("script").filter(function(){ return this.src.match(/jquery\.mobile/)})[0].src,
			reg = new RegExp("(.*?)\/\\w+\/jquery\.mobile-" + version +"(?:\.min)?\.js$");
		
		path = path.match(reg)[1];
		
		loadJSON( path + "/" + ( mobile.i18nFolder || defaultFolder ) ) ;
        */
		loadJSON(defaultFolder);
		// ??????????????
		$(document).bind("pagebeforecreate", function( evt ) { 
			var $page = $( evt.target );
			i18n.applyI18n( $().add( $page ).add( $page.find( "script" ) ) );
		});
   //}

})( jQuery, this );