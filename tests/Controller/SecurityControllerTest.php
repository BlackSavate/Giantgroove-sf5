<?php

namespace App\Tests\Controller;

use App\Entity\User;

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

        // TODO: test flashes
        $this->client->request('POST', '/login', ['_username' => 'fake_username', '_password' => $user->getPassword()]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', '/login', ['_username' => $user->getId(), '_password' => 'fake_password']);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testRegister() {
        $this->client->request('GET', '/register');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->client->request('POST', '/register', [
            'app_user[username]' => 'test',
            'app_user[firstname]' => 'test',
            'app_user[lastname]' => 'test',
            'app_user[password][first]' => 'Totoro123!',
            'app_user[password][second]' => 'Totoro123',
            'app_user[email][first]' => 'mail@mail.com',
            'app_user[email][second]' => 'mail@mail.com',
            'app_user[address1]' => 'test',
            'app_user[address2]' => 'test',
            'app_user[zipcode]' => 42424,
            'app_user[city]' => 'test',
            'app_user[country]' => 'test',
            'app_user[birthdate][month]' => 1,
            'app_user[birthdate][day]' => 1,
            'app_user[birthdate][year]' => 1970,
            'app_user[avatar]' => null,
        ]);
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
        $repo = $this->em->getRepository(User::class);
        $user = $repo->findOneByUsername('test');
        $this->assertNotNull($user);
    }
}
