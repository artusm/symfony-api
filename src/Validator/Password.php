<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class Password extends Constraint
{
    public string $messageEmpty = 'assert.password.empty';
    public string $messageInvalid = 'assert.roles.invalid';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}