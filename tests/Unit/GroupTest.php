<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use App\Group;

class GroupTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

    // public function setUp(): void {
    //     parent::setUp();
    // }

    public function testPost()
    {
        $group = factory(\App\Group::class, 1)->create()->toArray();

        $this->assertDatabaseHas('groups', [
            'name' => $group[0]['name']
        ]);
    }
}
