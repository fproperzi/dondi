
var routes = [
	{ path: '/'													,url: './index.html'		,alias: ['/home/','/dondi/']
		},{ path: '/about/'												,url: './pages/about.html'
		},{ path: '/form/'												,url: './pages/form.html'
		},{ path: '/users/'												,componentUrl: './pages/users.html'
		
		
		},{ path: '/amministrazione/'									,componentUrl: './pages/amministrazione.html'		//,ignoreCache: true
		},{ path: '/dynamic-route/blog/:blogId/post/:postId/'			,componentUrl: './pages/dynamic-route.html'
		},{ path: '/request-and-load/user/:userId/'						,async: vfreqandload
	//},{ path: '/authors/'									,componentUrl: './pages/authors.html'
	},{ path: '/impianti/tipi/'									,async: vf_async	,page: './pages/tipi_impianto.html'
	},{ path: '/impianti/tipi/:tipo_id/'						,async: vf_async	,page: './pages/impianti_x_tipo.html'
	},{ path: '/impianti/comuni/'								,async: vf_async	,page: './pages/nu_impianti_x_comune.html'
	},{ path: '/impianti/comuni/:comune_id/tipi/'				,async: vf_async	,page: './pages/tipi_impianto_x_comune.html'
	},{ path: '/impianti/comuni/:comune_id/tipi/:tipo_id/'		,async: vf_async	,page: './pages/impianti_x_tipo_x_comune.html'
	},{ path: '/impianti/:impianto_id/interventi'				,async: vf_async	,page: './pages/interventi_x_impianto.html'
	},{ path: '/impianti/interventi/:intervento_id/'			,async: vf_async	,page: './pages/attivita_x_intervento_x_impianto.html'
	
	},{ path: '/authors/'										,async: vf_async			,page: './pages/authors5.html'
	
	// Default route (404 page). MUST BE THE LAST --------------------------------------------------		
	},{ path: '(.*)',componentUrl: './pages/404.html?'+(Date.now()) 	
}];



function vf_async(routeTo, routeFrom, resolve, reject){
	//var router = this, app = router.app;	
	app.preloader.show();
	app.request.getJSON(c_api_url + routeTo.url.replace(/\/$/, ''),{},
		function(data, status, xhr){    // success
			resolve(
				{ componentUrl: routeTo.route.page},
				{ context: {rs: data}},
			);
			app.preloader.hide();
		},
		function(data, status, xhr){	// error
			reject(
				{ componentUrl: './pages/404.html'},
				{ context: {rs: data}},
			);
			app.preloader.hide();
		},
	);
}



function vfreqandload(routeTo, routeFrom, resolve, reject) {
	
	var router = this;		// Router instance
	var app = router.app;	// App instance	
	
	app.preloader.show();	// Show Preloader
	var userId = routeTo.params.userId;	 // User ID from request
	
	// Simulate Ajax Request
	setTimeout(function () {
		// We got user data from request
		var user = {
			firstName: 'Vladimir',
			lastName: 'Kharlampidi',
			about: 'Hello, i am creator of Framework7! Hope you like it!',
			links: [
				{ title: 'Framework7 Website'	,url: 'http://framework7.io'},
				{ title: 'Framework7 Forum'		,url: 'http://forum.framework7.io'}
			]
		};
		app.preloader.hide();
		resolve(
			{ componentUrl: './pages/list.html'	},
			{ context: {user: user,}			}
		);
		
		
	}, 1000);
}			