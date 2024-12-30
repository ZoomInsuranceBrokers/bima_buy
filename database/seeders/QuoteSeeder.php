<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Get the list of lead IDs
        $leadIds = DB::table('leads')->pluck('id');

        // Create 10 sample quotes for random leads
        foreach (range(1, 10) as $index) {
            DB::table('quotes')->insert([
                'lead_id' => $faker->randomElement($leadIds),
                'quote_name' => $faker->sentence(3),
                'price' => $faker->randomFloat(2, 100, 5000), // Random quote between 100 and 5000
                'description' => json_encode([
                    json_encode([
                        $faker->word, 
                        $faker->word,
                        $faker->word,
                        $faker->word,
                        $faker->word,
                    ]),
                ]),
                'is_accepted' => $faker->boolean(),
                'payment_status' => $faker->boolean(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
