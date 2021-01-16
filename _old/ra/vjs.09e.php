<!DOCTYPE html> 
<html><head>
<title>videojs</title><meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/video.js/6.6.0/alt/video-js-cdn.css"/>
<link type="text/css" rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.css" />
<script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.min.js"></script>
<script>//window.VIDEOJS_NO_DYNAMIC_STYLE = true;</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/6.6.0/video.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-youtube/2.4.1/Youtube.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-flash/2.1.0/videojs-flash.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/5.12.2/videojs-contrib-hls.min.js"></script>
<!--script src="vjs/videojs-time-offset.js"></script-->
<!--script src="videojs-offset.js"></script-->
<script src="//cdn.sc.gl/videojs-hotkeys/latest/videojs.hotkeys.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.js"></script>
<script type="text/javascript">
var gplayer,gplayerId,gbSeeked=false;
var gjpList = [
 {title:"ocean"		                  							,src:"https://vjs.zencdn.net/v/oceans.webm"}
,{title:"ocean" 	,start:2 		                			,src:"https://vjs.zencdn.net/v/oceans.webm"}
,{title:"ocean" 	,start:10 		                			,src:"https://vjs.zencdn.net/v/oceans.webm"}
,{title:"ocean" 	,start:19		                			,src:"https://vjs.zencdn.net/v/oceans.webm"}
,{title:"ocean" 	,start:30		                			,src:"https://vjs.zencdn.net/v/oceans.webm"}
,{title:"ocean" 	,start:40		                			,src:"https://vjs.zencdn.net/v/oceans.webm"}

,{title:"test m4v" 	,start:0	,type:'video/mp4'				,src:"http://mirrors.standaloneinstaller.com/video-sample/dolbycanyon.m4v"}
,{title:"test m3u8" ,start:0	,type:'application/x-mpegURL'	,src:"http://playertest.longtailvideo.com/adaptive/bbbfull/bbbfull.m3u8"}
,{title:"test rtmp" ,start:0	,type:"rtmp/mp4"    			,src:"rtmp://s1sxtap7w1poox.cloudfront.net/cfx/st/&mp4:ca5982e967fcc1c9bdd77f2684056167.360p"}
,{title:"test flv" 	,start:0	,type:'video/flv'	      		,src:"http://techslides.com/demos/sample-videos/small.flv"}


,{title:"twitter"                       						,src:"http://podcast.rickygervais.com/rickyandpepe_twitter.mp4"		}
,{title:"flower"                        						,src:"http://distribution.bbb3d.renderfarming.net/video/mp4/bbb_sunflower_1080p_60fps_normal.mp4"		}
,{title:"game"                          						,src:"http://assets3.ign.com/videos/zencoder/2016/06/15/640/7080c56a76e2b74ec8ecfe4c224441d4-500000-1466028542-w.mp4"		}

,{title:"yt"		                      						,src:"uVqTVo5XFpI"  }
,{title:"yt-jumpto-120"  		,start:120   ,end:125 			,src:"iz73t1pdtno"  }
,{title:"yt-jumpto-240"  		,start:240   ,end:244 			,src:"8vBwj3I8t7Y"  }
,{title:"yt-jumpto-1400"		,start:1400  ,end:1410			,src:"iz73t1pdtno"  }


,{title:"fra s8A2L++"			,start:5661	,end:5683			,src:"OJo-hcmsKvg"}
,{title:"fra s8D5R++"			,start:1696	,end:1729			,src:"OJo-hcmsKvg"}
,{title:"yt-se1"				,start:1400 ,end:1405 			,src:"iz73t1pdtno"  }
,{title:"fra l7A2R+d"			,start:3245	,end:3409			,src:"OJo-hcmsKvg"}
,{title:"smr lqD2R+"			,start:2797	,end:2834			,src:"OJo-hcmsKvg"}
,{title:"yt-se2"				,start:1000 ,end:1005 			,src:"iz73t1pdtno"  }
,{title:"fra koA2L++@"			,start:5711	,end:5740			,src:"OJo-hcmsKvg"}

];					
			
    function getJsonFromUrl() {
        var query = location.search.substr(1) + location.hash;
        var result = {};
        query.split(/&|#/).forEach(function(part) {
            var item = part.split("=");
            if (item.length < 2)
                return;
            result[item[0]] = decodeURIComponent(item[1]);
        });
        return result;
    }
	

!function($, e, o) {
    $(document).on("pagebeforechange", function(e, t) {
        if ("string" == typeof t.toPage) {
            console.log(t.toPage);
            var p = $.mobile.path.parseUrl(t.toPage);
            if ($.mobile.path.isEmbeddedPage(p)) {
                var h = {}; if(t.options.link) h=$.extend(h,t.options.link.context.dataset);
				var i = $.mobile.path.parseUrl(p.hash.replace(/^#/, ""));
                if (i.search) {
                    var r, n, s, l, g = (i.search || "").replace(/^\?/, "").split(/&/);
                    for (r = 0; r < g.length; r++) {
                        var f = g[r]; 
                        f && (n = f.split(/=/),
                        s = n[0],
                        l = n[1],
                        h[s] === o ? h[s] = l : ("object" != typeof h[s] && (h[s] = [h[s]]),
                        h[s].push(l)))
                    }
                    t.options.dataUrl || (t.options.dataUrl = t.toPage),
                    t.options.pageData = h,
                    t.toPage = p.hrefNoHash + "#" + i.pathname
                }
                //if anchor get data <a href="xxx" data-test="test!">....
                if( $(e.currentTarget.activeElement).attr("href") ) h=$.extend(h,$(e.currentTarget.activeElement).data());
                $.mobile.pageData = h; 
            }
        }
    })
}(jQuery, window);
//yt time seek  : ?t=23m:04s
function sfYTms(s){return(s-(s%=60))/60+(9<s?'m':'m0')+s+'s'}

// contatore gplayerId = ++gplayerId % (gjpList.length+1)
//https://jsfiddle.net/onigetoc/ytntz2ux/
function vfLoad4List(){
	var yt = "https//www.youtube.com/embed/"
	   ,ext = ['mp4','flv','webm','m4v','m3u8']
	   ,e = gjpList[gplayerId]
	   ,x = e.src.split('.').pop().toLowerCase()  // estrae estensione
	   ,s,t;

	e.start = e.start || 0;
	//e.end   = e.start +5;	   
	t = e.type || ((e.src.indexOf('rtmp:/')>=0 ? 'rtmp/' : 'video/') + (ext.indexOf(x) < 0 ? "youtube" : x));
	
	//console.log('prossimo video:');
	console.log( $.extend(e,{type:t}) );	
	$('#p-video-title').html(e.title);
	
	if(ext.indexOf(x) < 0) s = yt+e.src;
	else                   s = e.src;
		
		
		// if (e.start && e.end) {
		// 	gplayer.options_.youtube.start = e.start;
		// 	gplayer.options_.youtube.end = e.end;
		// }
		
		//if (e.start && e.end) s += '?start='+e.start+'&end='+e.end;
		//else if(e.start)      s += '?t='+ sfYTms(e.start);
		
	gplayer.src({type:t,src:s});
	gplayer.play();

	//if(e.start) 
	if (e.start) {
		gbSeeked = false;
		gplayer.currentTime(e.start);
	}
	else gbSeeked = true;
	//gbSeeked = e.start ? false : true;  // e.start=0 no jump seek=true
	//gplayer.currentTime(e.start);

	
	//?start=53&end=59


	//if($('button.vjs-big-play-button')) $('button.vjs-big-play-button').click();
	//else if($('button.vjs-play-control')) $('button.vjs-play-control').click();
}
$.mobile.document.on( "pagecreate","#p-home"  ,function(){
	var h=[];
	gjpList.forEach(function(e,i) {
		h.push('<li><a href="#p-video?i=',i,'">',e.title,'</a></li>');
	});
	$('#p-home-video-ul').empty().html( h.join('') ).listview('refresh');
	
});
$.mobile.document.on( "pagecreate","#p-video" ,function(){
	gplayer = videojs('my-video', { 
		 techOrder: ["html5","flash","youtube"] 
		,playbackRates: [0.5,1,4] 
		,autoplay: 1
		,youtube: { iv_load_policy: 1, origin: location.origin } 
		//,"plugins": { "resolutionSelector": { "default_res": "360" } } 
		//,controlBar: {
        //    children: ["playToggle", "volumeMenuButton", "currentTimeDisplay", "timeDivider", "durationDisplay", "progressControl", "liveDisplay", "playbackRateMenuButton", "captionsButton", "remainingTimeDisplay", "fullscreenToggle", ]
        //}

		}, function () {
			//console.log('Good to go!');
			//this.play();

			console.log('ready!')

			this.on('loadstart', function() {
				console.log("loadstart:"+gjpList[gplayerId].src);
				const e = gjpList[gplayerId]
				    , c = this.currentTime(); 
				if(e && e.start && c < e.start && this.isReady_) this.currentTime(e.start);
				
			});
			/*
			this.on("seeking", function (e) {
				sseek = true; console.log('seeking');
			});
			*/
			this.on("seeked", function (e) {
				gbSeeked = true; console.log('seeked');
			});
			
			this.on('timeupdate', function () {
				var e = gjpList[gplayerId]
				   ,c = this.currentTime(); 
				
				$('#p-video-txt').html(e.start+" :: "+Math.floor(c)+" :: "+e.end);
				if(gbSeeked) console.log(Math.floor(c));
				
				//if(e.start && c < e.start && this.isReady_) this.currentTime(e.start);
				if(gbSeeked && e.end && e.end < c )     {
					
						this.trigger('ended');

				}
					
			});
			
			
			this.on('ended', function () {
					isEndedTriggered = true;
				//this.pause();
				gplayerId = ++gplayerId % gjpList.length;
				vfLoad4List();
				
			});
			

		}).hotkeys({
				volumeStep: 0.1,
				seekStep: 5,
				alwaysCaptureHotkeys: true,
				enableJogStyle: true
		});	
		
/*		

	//https://codepen.io/onigetoc/pen/wJRyvZ
var Button = videojs.getComponent('Button');
// Extend default
var PrevButton = videojs.extend(Button, {
  //constructor: function(player, options) {
  constructor: function() {
    Button.apply(this, arguments);
    //this.addClass('vjs-chapters-button');
    this.addClass('icon-angle-left');
    this.controlText("Previous");
  },
  handleClick: function() {
    console.log('click');
    player.playlist.previous();
  }
});	
// Extend default
var NextButton = videojs.extend(Button, {
  //constructor: function(player, options) {
  constructor: function() {
    Button.apply(this, arguments);
    //this.addClass('vjs-chapters-button');
    this.addClass('icon-angle-right');
    this.controlText("Next");
  },
  handleClick: function() {
    console.log('click');
    player.playlist.next();
  }
});
// Register the new component
videojs.registerComponent('NextButton', NextButton);
videojs.registerComponent('PrevButton', PrevButton);
//player.getChild('controlBar').addChild('SharingButton', {});
player.getChild('controlBar').addChild('PrevButton', {}, 0);
player.getChild('controlBar').addChild('NextButton', {}, 2);
*/


});

$.mobile.document.on( "pageshow", "#p-video"  ,function(){ 
    if($.mobile.pageData.i) gplayerId = $.mobile.pageData.i;
	vfLoad4List();
	
});
$.mobile.document.on( "pagebeforehide", "#p-video", function(e,ui){  
	//var player = videojs('my-video');
	gplayer.pause();
});


</script>
<style>
/* https://stackoverflow.com/questions/10790453/videojs-with-jquery-mobile-transitions
**/
.ui-page {
        -webkit-backface-visibility: hidden;
}
.video-js {
    height: auto;
}
.video-holder,
.video-holder * {
  box-sizing: border-box !important
}

.video-holder {
  background: #1b1b1b;
  padding: 10px
}

.centered {
  width: 100%
}

#video {
  border-radius: 8px
}

.video-holder .vjs-big-play-button {
  left: 50%;
  width: 100px;
  margin-left: -50px;
  height: 80px;
  top: 50%;
  margin-top: -40px
}


/* CUSTOM BUTTONS */
[class^="icon-"]:before,
[class*=" icon-"]:before {
  font-family: FontAwesome;
  font-weight: normal;
  font-style: normal;
  display: inline-block;
  text-decoration: inherit;
}
.icon-angle-left:before {
    content: "\f104";
}
.icon-angle-right:before {
    content: "\f105";
}

.video-js .icon-angle-right, .video-js .icon-angle-left {
    cursor: pointer;
    -webkit-box-flex: none;
    -moz-box-flex: none;
    -webkit-flex: none;
    -ms-flex: none;
    flex: none;
}

</style>
</head> 
<body> 


<div id="p-home" data-role="page" data-theme="a">
	<div data-role="header" data-position="fixed" >
		<h1>videojs</h1>
	</div>  <!-- /header -->
	<div data-role ="content" >	
		<ul data-role="listview" id="p-home-video-ul">
		</ul>		
	</div>  <!-- /content -->
</div>  <!-- /page home -->
<div id="p-video" data-role="page" data-theme="a">
	<div data-role="header" >
        <a href="#p-home" data-corners="true" data-icon="back" data-direction="reverse" >Back</a>
		<h1 id="p-video-title">video</h1>
   	</div>  <!-- /header -->
	<div data-role="content" style="padding: 0px;">  
		<video id="my-video" 
		controls preload="auto" poster="rugbyassistant.png"
		class="video-js vjs-default-skin vjs-16-9 vjs-big-play-centered">
		</video>
		<div id="p-video-txt"></div>
    </div>  <!-- /content -->
</div>  <!-- /page --> 





</body></html>
