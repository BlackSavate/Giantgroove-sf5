<?php
declare(strict_types = 1);

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

abstract class AbstractControllerTest extends WebTestCase {
    /** @var  Application $application */
    protected static $application;
    /** @var KernelBrowser */
    protected $client;
    /** @var  EntityManager $em */
    protected $em;
    protected $persistedFixtures = array();

    public function setUp() {
        static::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->em = self::$container->get('doctrine.orm.entity_manager');
    }

    public function tearDown(): void
    {
        foreach ($this->persistedFixtures as $persistedFixture) {
            $repo = $this->em->getRepository($persistedFixture['entityClass']);
            $entity = $repo->find($persistedFixture['id']);
            $this->em->remove($entity);
        }
        $this->em->flush();
        $this->client = null;
        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

    static function persistFixture(array $entities) {
        $em = self::$container->get('doctrine.orm.entity_manager');
        foreach ($entities as $e) {
            $em->persist($e);
        }
        $em->flush();
    }

    protected function save($className, $id)
    {
        $this->persistedFixtures[] = [
            'entityClass' => $className,
            'id' => $id
        ];
    }

    protected function loadAndSave(string $class, string $mode = 'simple', array$attr = []){
        $fixtureClass = 'App\Tests\DataFixtures\\'.ucfirst($class).'Fixtures';
        $method = 'load'.ucfirst($mode);
        $entity = $fixtureClass::$method($attr);
        $this->save('App\Entity\\'.ucfirst($class), $entity->getId());
        return $entity;
    }
}


