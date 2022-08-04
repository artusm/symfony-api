<?php

namespace App\Tests;

use App\Controller\RestfulController;
use App\Tests\Abstracts\ApiPlatformTestCase;
use App\Tests\Contracts\ApiGroupCrudTestInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiGroupCrudTest extends ApiPlatformTestCase implements ApiGroupCrudTestInterface
{
    protected string $apiEndpoint = '/api-v2/groups';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testListGroups(): void
    {
        static::createClient()->request('GET', $this->apiEndpoint, [
            'headers' => $this->requestHeaders(),
        ]);

        $this->assertSuccessfulJson([
            [
                'id'    => 1,
                'name'  => 'group-01',
                'users' => [
                    [
                        'id'    => 1,
                        'name'  => 'user-01',
                        'email' => 'user-01@email.com',
                    ],
                    [
                        'id'    => 2,
                        'name'  => 'user-02',
                        'email' => 'user-02@email.com',
                    ]
                ],
            ],
            [
                'id'    => 2,
                'name'  => 'group-02',
                'users' => [
                    [
                        'id'    => 2,
                        'name'  => 'user-02',
                        'email' => 'user-02@email.com',
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
    public function testCreateGroup(): void
    {
        static::createClient()->request('POST', $this->apiEndpoint, [
            'headers' => $this->requestHeaders(),
            'json' => [
                'name'  => 'group',
                'users' => [1, 2],
            ]
        ]);

        $this->assertSuccessfulJson([
            'message'   => RestfulController::ENTITY_HAS_BEEN_CREATED,
            'data'      => [
                'id'    => 3,
                'name'  => 'group',
                'users' => [
                    [
                        'id'    => 1,
                        'name'  => 'user-01',
                        'email' => 'user-01@email.com',
                    ],
                    [
                        'id'    => 2,
                        'name'  => 'user-02',
                        'email' => 'user-02@email.com',
                    ]
                ],
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetGroup(): void
    {
        static::createClient()->request('GET', $this->apiEndpoint.'/1');

        $this->assertSuccessfulJson([
            'id'    => 1,
            'name'  => 'group-01',
            'users' => [
                [
                    'id'    => 1,
                    'name'  => 'user-01',
                    'email' => 'user-01@email.com',
                ],
                [
                    'id'    => 2,
                    'name'  => 'user-02',
                    'email' => 'user-02@email.com',
                ]
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
    public function testUpdateGroup(): void
    {
        static::createClient()->request('PUT', $this->apiEndpoint.'/1', [
            'json' => [
                'name'  => 'group-0',
                'users' => [2]
            ]
        ]);

        $this->assertSuccessfulJson([
            'message'   => RestfulController::ENTITY_HAS_BEEN_UPDATED,
            'data'      => [
                'id'    => 1,
                'name'  => 'group-0',
                'users' => [
                    [
                        'id'    => 2,
                        'name'  => 'user-02',
                        'email' => 'user-02@email.com',
                    ]
                ],
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testDeleteGroup(): void
    {
        static::createClient()->request('DELETE', $this->apiEndpoint.'/1');
        $this->assertSuccessfulJson([
            'message' => RestfulController::ENTITY_HAS_BEEN_DELETED,
        ]);
    }
}
