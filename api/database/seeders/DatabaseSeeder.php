<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
		$this->call([
			//LookupSeeder::class,
			UserSeeder::class,
			AuthorSeeder::class,
			CodiceCerSeeder::class,
			ConferimentoSeeder::class,
			AuthorSeeder::class,
			ImpiantoSeeder::class,

		]);
    }
}
