<?php

namespace gvanto\addressbookapi\Tests;

use gvanto\addressbookapi\Models\Person;
use Tests\TestCase;

class SearchPersonsByNameTest extends TestCase
{
    /**
     * Find person(s) by first name
     *
     * @return void
     */
    public function testByFirstName()
    {
        /** @var Person $person */
        $person = Person::inRandomOrder()->first();

        $response = $this->get('/addressbookapi/persons/find-by-name?first_name=' . $person->first_name);

        //$response->dump();

        $response->assertStatus(200);

        //Note: better test would be to check if person's ID matches that of person searched...
    }

    /**
     * Find person(s) by last name
     *
     * @return void
     */
    public function testByLastName()
    {
        /** @var Person $person */
        $person = Person::inRandomOrder()->first();

        $response = $this->get('/addressbookapi/persons/find-by-name?last_name=' . $person->last_name);

        //$response->dump();

        $response->assertStatus(200);
    }

    /**
     * Find person(s) by both first + last
     *
     * @return void
     */
    public function testByBoth()
    {
        /** @var Person $person */
        $person = Person::inRandomOrder()->first();

        $response = $this->get(
            sprintf('/addressbookapi/persons/find-by-name?first_name=%s&last_name=%s',
                $person->first_name,
                $person->last_name
            )
        );

        //$response->dump();

        $response->assertStatus(200);
    }

}
