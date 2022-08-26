<?php

namespace gvanto\addressbookapi;

use gvanto\addressbookapi\Models\Group;
use gvanto\addressbookapi\Models\Person;

class AddressBookService
{
    /**
     * @param string $first_name
     * @param string $last_name
     * @param array $phoneNumbers
     * @param array $addresses
     * @param array $emails
     * @return Person
     */
    public function createPerson(
        string $first_name,
        string $last_name,
        array $phoneNumbers,
        array $addresses,
        array $emails
    ): Person
    {
        /** @var Person $person */
        $person = Person::make([
            'first_name' => $first_name,
            'last_name' => $last_name,
        ]);

        $person->phone_numbers = $phoneNumbers;
        $person->addresses = $addresses;
        $person->save();

        $this->addEmailsToPerson($emails, $person);

        return $person;
    }

    /**
     * @param array $emails
     * @param Person $person
     */
    public function addEmailsToPerson(array $emails, Person $person): void
    {
        foreach ($emails as $email) {
            $person->addEmail($email);
        }
    }

    /**
     * @param string $name The name of the group
     * @return Group
     */
    public function createGroup(string $name): Group
    {
        return Group::create([
            'name' => $name,
        ]);
    }

}
