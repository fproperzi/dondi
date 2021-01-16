var $$ = Dom7;
var T7 = Template7;


const c_api_url = 'api/public';
const c_usr_img = 'assets/img/user.png';


//https://forum.framework7.io/t/localization-and-how-i-solved-it/9688
window.language = (localStorage.getItem('language') || (window.navigator.language || 'it_IT')).substring(0,2);
window.localize = (key) => window.locales[window.language][key] ? window.locales[window.language][key] : key;

//-- template helpers
T7.registerHelper('_e', v => window.localize(v));
T7.registerHelper('_imgUsr', v=> (v || c_usr_img ));  // se immagine utente è null|undefinited mette quella di default
T7.registerHelper('_get', (a,b,c) => _get(a,b,c));
T7.registerHelper('_tData', v=> (new Date(v).toLocaleString(window.language,{day: "numeric"})+' <small>'+(new Date(v).toLocaleString(window.language,{month:'short',year:'numeric'}).toLocaleUpperCase())+'</small>'));
T7.registerHelper('_tTime', v=> (new Date(v).toLocaleTimeString(window.language,{timeStyle: "short"})));

//https://stackoverflow.com/questions/47023211/better-way-to-get-property-than-using-lodash
function _get(object, path, defval = null) {
    if (typeof path === "string") path = path.replace(/\[/g, '.').replace(/\]/g,'').split('.');
    return path.reduce((xs, x) => (xs && xs[x] ? xs[x] : defval), object);
}

var app = new Framework7({
	root: '#app', // App root element
	name: 'Dondi AcqueReflue', // App name
	theme: 'auto', // Automatic theme detection
	
	cache: false,
	
	//sync data() {
	//	const impianti = await fetch(apiUrl+'/impianti').then(res => res.json());
	//	return {
	//		impianti,  
	//	};
	//,
	data: function () { // App root data
		return {
			bOpenMapOnInit: false,
			gps: [],
			user: {
				firstName: 'John',
				lastName: 'Doe',
			},
		};
	},
	methods: {	// App root methods
		helloWorld: function () {
			app.dialog.alert('Hello World!');
		},
		// creazione mappa con el: <div id="map123"></div>, rs: array imianti con longitude e latitude
		initMap: function(el,rs, zoom=9, lat=46.35, lng=12.25) {
			let r = Array.isArray(rs) && rs[0]?.latitude ?rs:[]
			  , m = new google.maps.Map(document.getElementById(el), { 
					zoom: zoom, 
					center: new google.maps.LatLng(lat, lng),
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					//mapTypeId: "roadmap",
					//disableDefaultUI: true,
					zoomControl: true,
					mapTypeControl: false,
					scaleControl: false,
					streetViewControl: false,
					rotateControl: false,
					fullscreenControl: true,
				});
				
			r.forEach(v=>(
				new google.maps.Marker({
					position: new google.maps.LatLng(v.latitude, v.longitude),
					title: v.codice,
					label: ({'Depur':'D','Desab':'S','I-Dec':'Z','Imhof':'I','Sfior':'F','Solle':'T'})[v.tipo.substr(0,5)]||'X',
					map: m,
				})
			));
		},
		cageData: function(lat,lng) {
			const apiKey = 'baa701db30514ba2be9237280077b852';
			const apiUrl = 'https://api.opencagedata.com/geocode/v1/json'
			var s = lat+'_'+lng
			  , d = {
					'key': apiKey,
					'q':  lat + ',' + lng,  //encodeURIComponent(latitude + ',' + longitude),
					'language': window.language,
					'pretty': 1,
					'no_annotations': 1,
			  };
			if(! app.data.gps[s]) app.request.json(apiUrl,d,function(res){
				app.data.gps[s] = res.results[0]?.formatted;
			});

			return app.data.gps[s];
		},
		//---https://stackoverflow.com/questions/38552003/how-to-decode-jwt-token-in-javascript-without-using-a-library
		parseJWT: function() {
			var token = window.localStorage['token'];
			var base64Url = token.split('.')[1];
			var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
			var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
				return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
			}).join(''));

			return JSON.parse(jsonPayload);
		}
	},
	routes: routes, // App routes
	serviceWorker: {  path: './service-worker.js'},// Register service worker
});



// Login Screen Demo
$$('#my-login-screen .login-button').on('click', function () {
  var username = $$('#my-login-screen [name="username"]').val();
  var password = $$('#my-login-screen [name="password"]').val();

  // Close login screen
  app.loginScreen.close('#my-login-screen');

  // Alert username and password
  app.dialog.alert('Username: ' + username + '<br>Password: ' + password);
});