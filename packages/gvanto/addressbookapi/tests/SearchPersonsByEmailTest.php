<?php

namespace gvanto\addressbookapi\Tests;

use gvanto\addressbookapi\Models\Person;
use Tests\TestCase;

class SearchPersonsByEmailTest extends TestCase
{
    /**
     * Find person(s) by first name
     *
     * @return void
     */
    public function testFullEmail()
    {
        /** @var Person $person */
        $person = Person::inRandomOrder()->first();

        $response = $this->get('/addressbookapi/persons/find-by-email?email=' . $person->emails->first()->email);

        //$response->dump();

        $response->assertStatus(200);
    }

    /**
     * Find person(s) by first name
     *
     * @return void
     */
    public function testSubstring()
    {
        /** @var Person $person */
        $person = Person::inRandomOrder()->first();

        $substr = substr($person->emails->first()->email, 0, 4);

        $response = $this->get('/addressbookapi/persons/find-by-email?email=' . $substr);

        $response->dump();

        $response->assertStatus(200);
    }
}
