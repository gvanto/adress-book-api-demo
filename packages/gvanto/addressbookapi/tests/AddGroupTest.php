<?php

namespace gvanto\addressbookapi\Tests;

use Illuminate\Support\Str;
use Tests\TestCase;

class AddGroupTest extends TestCase
{
    /**
     * Add group test
     *
     * @return void
     */
    public function test()
    {
        $response = $this->post('/addressbookapi/groups/add', [
            'name' => 'TestGroup_' . Str::random(4),
        ]);

        //$response->dump();

        $response->assertStatus(200);
    }
}
