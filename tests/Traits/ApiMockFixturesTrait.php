<?php

namespace App\Tests\Traits;

trait ApiMockFixturesTrait
{
    protected array $expectedGroup1 = [
        'id' => 1,
        'name' => 'group-01',
        'users' => [
            [
                'id' => 1,
                'name' => 'user-01',
                'email' => 'user-01@email.com',
            ],
            [
                'id' => 2,
                'name' => 'user-02',
                'email' => 'user-02@email.com',
            ]
        ],
    ];

    protected array $expectedGroup2 = [
        'id'    => 2,
        'name'  => 'group-02',
        'users' => [[
            'id' => 2,
            'name' => 'user-02',
            'email' => 'user-02@email.com',
        ]],
    ];

    protected array $expectedUser1 = [
        'id' => 1,
        'name' => 'user-01',
        'email' => 'user-01@email.com',
        'roles' => ['ROLE_GUEST'],
        'groups' => [
            [
                'id' => 1,
                'name' => 'group-01',
            ],
            [
                'id' => 2,
                'name' => 'group-02',
            ]
        ],
    ];

    protected array $expectedUser2 = [
        'id' => 2,
        'name' => 'user-02',
        'email' => 'user-02@email.com',
        'roles' => ['GUEST','ROLE_USER'],
        'groups' => [
            [
                'id' => 2,
                'name' => 'group-02',
            ]
        ],
    ];
}