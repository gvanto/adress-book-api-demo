<?php

namespace gvanto\addressbookapi\Tests;

use Faker\Generator;
use Illuminate\Support\Str;
use Tests\TestCase;


class AddPersonTest extends TestCase
{
    /**
     * Add person test
     *
     * @return void
     */
    public function test()
    {
        /** @var Generator $faker */
        $faker = app(Generator::class);

        $firstName = $faker->firstName;
        $lastName = $faker->lastName;

        $response = $this->post('/addressbookapi/persons/add', [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'emails' => [
                sprintf('%s@%s.com', strtolower($firstName), strtolower($lastName)),
                sprintf('%s.%s@gmail.com', strtolower($firstName), strtolower($lastName)),
            ],
            'phone_numbers' => [
                '0610624165',
                '0610624166',
            ],
            'addresses' => [
                $faker->address,
                $faker->address,
            ],
            'groups' => [
                $faker->colorName,
                $faker->colorName,
            ],
        ]);

        //$response->dump();

        $response->assertStatus(200);
    }
}
