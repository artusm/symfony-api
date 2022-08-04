<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ContainsRoles extends Constraint
{
    public string $messageRolesInvalid = 'assert.roles.invalid';
    public string $messageRolesEmpty = 'assert.roles.empty';
    public string $messageRolesMissing = 'assert.roles.missing';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}