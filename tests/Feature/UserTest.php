<?php

namespace Tests\Feature;

use TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLogin()
    {
        $response = $this->json('post', '/login', array(
            'username' => 'uriel',
            'password' => 'uriel',
            '_token'   => 'JhrBgk931KPeOgP3Smhin0JvDM2606xFIMu0TLCY'
        ));
        $response->assertStatus(200)->assertJson([
            'status' => 10000,
        ]);
    }
}
