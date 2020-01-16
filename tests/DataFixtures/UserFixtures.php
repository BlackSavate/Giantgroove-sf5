<?php
declare(strict_types = 1);

namespace App\Tests\DataFixtures;

use App\Entity\User;
use App\Tests\Controller\AbstractControllerTest;

class UserFixtures
{
    /**
     * @param array $attr Additional attributes
     * @return User
     * @throws \Exception
     */
    public static function loadSimple(array $attr = []): User {

        $now = new \DateTime();
        $user = new User();
        $user->setUsername('test_username');
        $user->setSlug('test_username');
        $user->setFirstName('test_firstname');
        $user->setLastName('test_lastname');
        $user->setEmail('test@mail.com');
        $user->setPassword('$2y$13$P8UttVhUA5xQIsa1Kn20BO6ogHxhup14uQwmvJFwFlLZXFX.WMdva');
        $user->setAddress1('42 Test street');
        $user->setZipcode(42424);
        $user->setCity('test_city');
        $user->setCountry('test_country');
        $user->setBirthdate($now);
        $user->setCreatedAt($now);
        $user->setIsActive(true);
        $user->setRoles(['ROLE_USER']);

        foreach ($attr as $key => $value) {
            $key = 'set' . ucfirst($key);
            $user->$key($value);
        }

        AbstractControllerTest::persistFixture([$user]);

        return $user;
    }
}
