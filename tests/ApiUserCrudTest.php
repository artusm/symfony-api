<?php

namespace App\Tests;

use App\Controller\RestfulController;
use App\Tests\Abstracts\ApiPlatformTestCase;
use App\Tests\Contracts\ApiUserCrudTestInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiUserCrudTest extends ApiPlatformTestCase implements ApiUserCrudTestInterface
{
    protected string $apiEndpoint = '/api-v2/users';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testListUsers(): void
    {
        static::createClient()->request('GET', $this->apiEndpoint, [
            'headers' => $this->requestHeaders(),
        ]);

        $this->assertSuccessfulJson([
            [
                'id'        => 1,
                'name'      => 'user-01',
                'email'     => 'user-01@email.com',
                'roles'     => ['USER'],
                'groups'    => [
                    [
                        'id'    => 1,
                        'name'  => 'group-01',
                    ],
                ],
            ],
            [
                'id'        => 2,
                'name'      => 'user-02',
                'email'     => 'user-02@email.com',
                'roles'     => ['ROLE_GUEST','ROLE_USER'],
                'groups'    => [
                    [
                        'id'    => 1,
                        'name'  => 'group-01',
                    ],
                    [
                        'id'    => 2,
                        'name'  => 'group-02',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCreateUser(): void
    {
        static::createClient()->request('POST', $this->apiEndpoint, [
            'headers' => $this->requestHeaders(),
            'json' => [
                'name'      => 'user-03',
                'email'     => 'user-03@email.com',
                'password'  => 'secret',
                'roles'     => ['ROLE_GUEST','ROLE_USER'],
                'groups'    => [1, 2],
            ],
        ]);

        $this->assertSuccessfulJson([
            'message'   => RestfulController::ENTITY_HAS_BEEN_CREATED,
            'data'      => [
                'id'        => 3,
                'name'      => 'user-03',
                'email'     => 'user-03@email.com',
                'roles'     => ['ROLE_GUEST','ROLE_USER'],
                'groups'    => [
                    [
                        'id'    => 1,
                        'name'  => 'group-01',
                    ],
                    [
                        'id'    => 2,
                        'name'  => 'group-02',
                    ]
                ],
            ],
        ]);
    }

    /**
     * @return void
     * @throws TransportExceptionInterface
     */
    public function testCreateNonUniqueUser(): void
    {
        $response = static::createClient()->request('POST', $this->apiEndpoint, [
            'headers' => $this->requestHeaders(),
            'json' => [
                'name'      => 'user-03',
                'email'     => 'user-03@email.com',
            ],
        ]);

        $this->assertResponseStatusCodeSame($response->getStatusCode(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetUser(): void
    {
        static::createClient()->request('GET', $this->apiEndpoint.'/1', [
            'headers' => $this->requestHeaders(),
        ]);

        $this->assertSuccessfulJson([
            'id'        => 1,
            'name'      => 'user-01',
            'email'     => 'user-01@email.com',
            'roles'     => ['USER'],
            'groups'    => [
                [
                    'id'    => 1,
                    'name'  => 'group-01',
                ],
            ],
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testUpdateUser(): void
    {
        static::createClient()->request('PUT', $this->apiEndpoint.'/1', [
            'headers' => $this->requestHeaders(),
            'json'=> [
                'email'     => 'user-01@email.com',
                'name'      => 'user-0',
                'roles'     => ['ROLE_GUEST','ROLE_USER'],
                'groups'    => [2],
            ],
        ]);

        $this->assertSuccessfulJson([
            'message'   => RestfulController::ENTITY_HAS_BEEN_UPDATED,
            'data'      => [
                'id'        => 1,
                'email'     => 'user-01@email.com',
                'name'      => 'user-0',
                'roles'     => ['ROLE_GUEST','ROLE_USER'],
                'groups'    => [
                    [
                        'id'    => 2,
                        'name'  => 'group-02',
                    ]
                ],
            ],
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testDeleteUser(): void
    {
        static::createClient()->request('DELETE', $this->apiEndpoint.'/1');
        $this->assertSuccessfulJson([
            'message' => RestfulController::ENTITY_HAS_BEEN_DELETED,
        ]);
    }
}
