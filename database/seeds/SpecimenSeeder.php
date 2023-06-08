<?php

use Illuminate\Database\Seeder;

class SpecimenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Specimen::class, 10)->create();
    }
}
