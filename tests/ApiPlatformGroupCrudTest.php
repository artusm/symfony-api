<?php

namespace App\Tests;

use App\Tests\Abstracts\ApiPlatformTestCase;
use App\Tests\Contracts\ApiGroupCrudTestInterface;
use Symfony\Contracts\HttpClient\Exception\{
    ClientExceptionInterface,
    DecodingExceptionInterface,
    RedirectionExceptionInterface,
    ServerExceptionInterface,
    TransportExceptionInterface
};


class ApiPlatformGroupCrudTest extends ApiPlatformTestCase implements ApiGroupCrudTestInterface
{
    protected string $apiEndpoint = '/api/groups';

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
                'name' => 'group',
                'users' => [1, 2],
            ],
        ]);

        $this->assertSuccessfulJson([
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
        static::createClient()->request('GET', $this->apiEndpoint.'/1', [
            'headers' => $this->requestHeaders(),
        ]);

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
            'headers' => $this->requestHeaders(),
            'json'=> [
                'name' => 'group-0',
                'users' => [2],
            ],
        ]);

        $this->assertSuccessfulJson([
            'id'    => 1,
            'name'  => 'group-0',
            'users' => [
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
     * @throws ClientExceptionInterface
     */
    public function testDeleteGroup(): void
    {
        static::createClient()->request('DELETE', $this->apiEndpoint.'/1');
        $this->assertResponseIsSuccessful();
    }
}
