<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $currentDate = new \Datetime();
        $user = new User();
        $user->setUsername('sbelaud');
        $user->setFirstname('Simon');
        $user->setLastname('BELAUD');
        $user->setSlug('simon-belaud');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'DivFol42'));
        $user->setEmail('blacksavate@outlook.fr');
        $user->setAddress1('83 Route de l\'Ormeau');
        $user->setZipcode(86180);
        $user->setCity('Buxerolles');
        $user->setCountry('France');
        $user->setBirthdate($currentDate);
        $user->setCreatedAt($currentDate);
        $user->setIsActive(true);
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $manager->persist($user);
        $manager->flush();
    }
}
