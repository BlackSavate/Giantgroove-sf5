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
        $this->crawler = $this->client->request('GET', '/register');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->submitForm('Créer', [
            'app_user[username]'=> 'test',
            'app_user[firstname]'=> 'test',
            'app_user[lastname]'=> 'toto',
            'app_user[password]'=> [
                'first' => 'Totoro123',
                'second' => 'Totoro123'
            ],
            'app_user[email]'=> [
                'first' => 'test@mail.com',
                'second' => 'test@mail.com',
            ],
            'app_user[address1]'=> '83 ROUTE DE L\'ORMEAU',
            'app_user[address2]'=>' ',
            'app_user[zipcode]'=> 86180,
            'app_user[city]'=> 'BUXEROLLES',
            'app_user[country]'=> 'France',
            'app_user[birthdate]' => [
                'year' => 1970,
                'month' => 1,
                'day' => 1
            ],
        ]);

        $repo = $this->em->getRepository(User::class);
        $user = $repo->findOneBy(['username' => 'test']);
        $this->assertNotNull($user);
        $this->addToPersistedFixtures($user);
    }
}
