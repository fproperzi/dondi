<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Model::unguard();

		//DB::table('users')->delete();
	    $keys = ['name','email','password','passwordf'];
		$values = array(
			['name'=>'kino'				    ],
			['name'=>'Matteo Properzi'		],
			['name'=>'Angeloni Kevin'		],
			['name'=>'Astorino Claudio'		],
			['name'=>'Bellumat Stivens'		],
			['name'=>'Da Rin Giuseppe'		],
			['name'=>'Da Ros Cinzia'		],
			['name'=>'De Bernardin Johnny'	],
			['name'=>'De Pellegrini Michele'],
			['name'=>'Grauso Agostino'		],
			['name'=>'Maccagnan Claudio'	],
			['name'=>'Michieli Igor'		],
			['name'=>'Murer Luca'			],
			['name'=>'Porcu Michele'		],
			['name'=>'Ravara Giorgio'		],
		);
		$rest = array('email'=>'fproperzi@gmail.com'	,'password'=>Hash::make('lola'),'passwordf'=>Crypt::encrypt('lola'))
		
		
		foreach ($values as $value) {
			User::create(array_merge($value,$rest));
		}

		Model::reguard();   
    }
}
