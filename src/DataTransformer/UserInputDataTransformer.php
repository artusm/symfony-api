<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\UserInput;
use App\Entity\User;
use App\Repository\GroupRepository;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


final class UserInputDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly GroupRepository $groupRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {}

    /**
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): User
    {
        $this->validator->validate($object);

        /**
         * @var User $user
         * @var UserInput $object
         */
        $user = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;

        if (!$user) {
            $user = new User();
            $user->setEmail($object->email);
            $user->setPassword(
                $this->userPasswordHasher->hashPassword($user, $object->email)
            );
        }

        $user->setName($object->name);
        $user->setRoles($object->roles);

        if (isset($object->groups) && count($object->groups) > 0) {
            $user->clearGroups();
            $groups = $this->groupRepository->findBy(['id' => $object->groups]);
            if ($groups) {
                $user->addGroups($groups);
            }
        }

        return $user;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof User) {
            return false;
        }

        return User::class === $to && null !== ($context['input']['class'] ?? null);
    }
}