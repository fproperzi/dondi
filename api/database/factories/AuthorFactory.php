<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AuthorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Author::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
		return [
			'name' 			=> $this->faker->name,
			'email' 			=> $this->faker->unique()->safeEmail,
			'streetAddress'	=> $this->faker->streetAddress ,
			'city'			=> $this->faker->city ,
			'phoneNumber'		=> $this->faker->phoneNumber,
			'company' 		=> $this->faker->company,
			'catchPhrase'		=> $this->faker->catchPhrase,
			'freeText'		=> $this->faker->text,
			'dt'				=> $this->faker->dateTimeThisCentury->format('Y-m-d'),
        ];
    }
}
