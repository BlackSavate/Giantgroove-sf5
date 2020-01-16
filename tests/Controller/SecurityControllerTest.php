<?php

namespace App\Tests\Controller;

class SecurityControllerTest extends AbstractControllerTest
{
    public function setUp() {
        parent::setUp();
    }
    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testLogin()
    {
        $user = $this->loadAndSave('user');

        $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', '/login', ['_username' => $user->getUsername(), '_password' => $user->getPassword()]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
