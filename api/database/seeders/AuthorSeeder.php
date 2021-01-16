<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Models\Author;




class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		

		//Author::factory()->times(50)->make();
		//factory('\App\Models\Author',50)->make();
		//\App\Models\Author::factory()->times(50)->create();
		//entity(Author::class, 3)->create();
		
		//$authors = Author::factory()->count(50)->create();
		$faker = \Faker\Factory::create('it_IT');
 		for($i=0;$i<50;$i++) {
			$j =  [
				'name' 			=> $faker->name,
				'email' 		=> $faker->unique()->safeEmail,
				'streetAddress'	=> $faker->streetAddress ,
				'city'			=> $faker->city ,
				'phoneNumber'	=> $faker->phoneNumber,
				'company' 		=> $faker->company,
				'catchPhrase'	=> $faker->catchPhrase,
				'freeText'		=> $faker->text,
				'dt'			=> $faker->dateTimeThisCentury->format('Y-m-d'),
			];
			
			Author::create($j);
		}
		
    }
}
