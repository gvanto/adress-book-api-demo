<?php

namespace gvanto\addressbookapi\Tests;

use gvanto\addressbookapi\Models\Person;
use Tests\TestCase;

class GetGroupMembersTest extends TestCase
{
    /**
     * Get group members by group name test
     *
     * @return void
     */
    public function testByGroupName()
    {
        /** @var Person $person */
        $person = Person::first();

        // Use a group which has at least one person
        $response = $this->get('/addressbookapi/groups/members?group=' . $person->groups->first()->name);

        //$response->dump();

        $response->assertStatus(200);
    }

    /**
     * Get group members by group id test
     *
     * @return void
     */
    public function testByGroupId()
    {
        /** @var Person $person */
        $person = Person::inRandomOrder()->first();

        // Use a group which has at least one person
        $response = $this->get('/addressbookapi/groups/members?group=' . $person->groups->first()->id);

        //$response->dump();

        $response->assertStatus(200);
    }
}
