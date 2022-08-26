<?php

namespace gvanto\addressbookapi\Tests;

use gvanto\addressbookapi\Models\Person;
use Tests\TestCase;

class GetPersonGroupsTest extends TestCase
{
    /**
     * Get person's member groups
     *
     * @return void
     */
    public function test()
    {
        /** @var Person $person */
        $person = Person::inRandomOrder()->first();

        // Use a group which has at least one person
        $response = $this->get('/addressbookapi/persons/groups?id=' . $person->id);

        //$response->dump();

        $response->assertStatus(200);
    }
}
