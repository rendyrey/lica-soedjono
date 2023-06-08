<?php

use Illuminate\Database\Seeder;

class AnalyzerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // factory(\App\Analyzer::class, 1)->create()->each(function ($analyzer){
        //     $group = factory(\App\Group::class)->make();
        //     $analyzer->group()->save($group);
        //     // $analyzer->group()->save()
        // });
        
        factory(\App\Group::class, 3)->create()->each(function($group){
            // $group = factory(\App\Group::class)->make();
            // $group->analyzers()->save($group);
            factory(\App\Analyzer::class, 5)->create(['group_id' => $group->id]);
        });
    }
}
