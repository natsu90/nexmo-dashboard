<?php

class InboundTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$faker = Faker\Factory::create();
		for ($i=0; $i < 50; $i++) {
			$inbound = Inbound::create(array(
				'from' => '601'.$faker->randomNumber(9),
				'to' => '601'.$faker->randomNumber(9),
				'type' => $faker->randomElement(array('text','unicode')),
				'text' => $faker->sentence()
			));
		}
	}

}
