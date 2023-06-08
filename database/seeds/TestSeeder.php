<?php

use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $specimen = factory(\App\Specimen::class)->create();
        $group = factory(\App\Group::class)->create();

        factory(\App\Test::class, 5)->create(['specimen_id' => $specimen->id, 'group_id' => $group->id]);
    }
}
