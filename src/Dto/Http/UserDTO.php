<?php

namespace App\Dto\Http;

use App\Validator\{
    ContainsRoles,
    Password,
};
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class UserDTO implements RequestDTOInterface
{
    #[Assert\NotNull(message: 'assert.name.is_null')]
    #[Assert\NotBlank(message: 'assert.name.not_blank')]
    #[Assert\Length(min: 2, max: 50)]
    private string $name;

    #[Assert\Email]
    private string $email;

    #[Password]
    private string $password;

    #[Assert\Type('array')]
    #[Assert\All(
       new Assert\Type(['type' => 'numeric']),
    )]
    private array $groups;

    #[Assert\Type('array')]
    #[Assert\All(
        new Assert\Type(['type' => 'string']),
    )]
    #[ContainsRoles]
    private array $roles;

    public function __construct(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $this->name = $content['name'] ?? '';
        $this->email = $content['email'] ?? '';
        $this->password = $content['password'] ?? '';
        $this->groups = $content['groups'] ?? [];
        $this->roles = $content['roles'] ?? [];
    }

    public function asObject(): object
    {
        $object = new \stdClass();
        $object->name = $this->name;
        $object->email = $this->email;
        $object->password = $this->password;
        $object->groups = $this->groups;
        $object->roles = $this->roles;

        return $object;
    }
}