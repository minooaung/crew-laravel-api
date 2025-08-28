<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organisation;

class OrganisationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Using factory to create 15 organisations with unique names
        //Organisation::factory(15)->create();


        // Create 15 organisations with unique names
        // This is a simple loop to create organisations
        for ($i = 1; $i <= 15; $i++) {
            Organisation::create([
                'name' => "Organisation {$i}",
            ]);
        }
    }
}
