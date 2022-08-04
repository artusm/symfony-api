<?php

namespace App\Tests\Jwt;

use App\Entity\User;
use App\Tests\Abstracts\ApiPlatformTestCase;
use Exception;
use \Symfony\Contracts\HttpClient\Exception\{
    TransportExceptionInterface,
    ServerExceptionInterface,
    RedirectionExceptionInterface,
    DecodingExceptionInterface,
    ClientExceptionInterface,
};

class AuthenticationTest extends ApiPlatformTestCase
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testLogin(): void
    {
        $client = self::createClient();
        $container = self::getContainer();

        $user = new User();
        $user->setName('John Dou');
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $container->get('security.user_password_hasher')->hashPassword($user, '$3CR3T')
        );

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        // retrieve a token
        $response = $client->request('POST', '/api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test@example.com',
                'password' => '$3CR3T',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('GET', '/', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }
}