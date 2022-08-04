<?php

namespace App\Tests\Abstracts;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\ApiFixtures;
use Doctrine\DBAL\Platforms\{
    SqlitePlatform,
};
use App\Entity\{
    User,
    Group,
};
use Symfony\Contracts\HttpClient\Exception\{
    ClientExceptionInterface,
    DecodingExceptionInterface,
    RedirectionExceptionInterface,
    ServerExceptionInterface,
    TransportExceptionInterface
};
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PasswordHasher\Hasher\{
    PasswordHasherFactory,
    UserPasswordHasher,
};

abstract class ApiPlatformTestCase extends ApiTestCase
{
    protected string $apiEndpoint = '/api';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function assertSuccessfulJson(array $assertData): void
    {
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains($assertData);
    }

    protected function requestHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        self::bootKernel();

        $this->truncateEntities([
            User::class,
            Group::class,
        ]);

        $this->loadFixture();
    }

    public function getEntityManager(): EntityManager
    {
        return self::$kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @throws Exception
     */
    private function truncateEntities(array $entities): void
    {
        $connection = $this->getEntityManager()->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();

        if ($databasePlatform instanceof SqlitePlatform) {
            $connection->executeQuery("PRAGMA foreign_keys = ON;");
        }

        foreach ($entities as $entity) {
            $table = $this->getEntityManager()->getClassMetadata($entity)->getTableName();
            $query = $databasePlatform->getTruncateTableSQL($table, true);

            if ($databasePlatform instanceof SqlitePlatform) {
                $connection->executeQuery(/** @lang SQLite */"
                    delete from sqlite_sequence where name='$table';
                ");
            }

            $connection->executeQuery($query);
        }
    }

    private function loadFixture(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $doctrine = $container->get('doctrine');
        $entityManager = $doctrine->getManager();

        $factory = $this->getPasswordHasherFactory();
        $userPasswordHasher = new UserPasswordHasher($factory);

        $fixture = new ApiFixtures($userPasswordHasher);
        $fixture->load($entityManager);
    }

    private function getPasswordHasherFactory(): PasswordHasherFactory
    {
        // Configure different password hashers via the factory
        $factory = new PasswordHasherFactory([
            User::class     => ['algorithm' => 'auto'],
            'common'        => ['algorithm' => 'bcrypt'],
            'memory-hard'   => ['algorithm' => 'sodium'],
        ]);

        // Retrieve the right password hasher by its name
        $passwordHasher = $factory->getPasswordHasher('common');

        // Hash a plain password
        $hash = $passwordHasher->hash('plain'); // returns a bcrypt hash

        // Verify that a given plain password matches the hash
        $passwordHasher->verify($hash, 'wrong'); // returns false
        $passwordHasher->verify($hash, 'plain'); // returns true (valid)

        return $factory;
    }
}