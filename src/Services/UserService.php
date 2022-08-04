<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\{
    GroupRepository,
    UserRepository,
};
use Doctrine\ORM\{
    EntityManagerInterface,
    NonUniqueResultException
};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserService extends AbstractEntityService
{
    public function __construct(
        EntityManagerInterface $entityManager,
        private readonly GroupRepository $groupRepository,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
        parent::__construct($entityManager);
    }

    public function list(): array
    {
        return $this->userRepository->findAll();
    }

    public function get(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    /**
     * @param object $dto as UserDTO
     * @throws NonUniqueResultException
     */
    public function create(object $dto): ?User
    {
        $user = $this->userRepository->findOneByEmail($dto->email);
        if ($user) return null;

        $user = new User();
        $user->setName($dto->name);
        $user->setEmail($dto->email);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, $dto->password)
        );
        $user->setRoles($dto->roles);

        if (!empty($dto->groups)) {
            $groups = $this->groupRepository->findBy(['id' => $dto->groups]);
            if ($groups) {
                $user->addGroups($groups);
            }
        }

        $this->persist($user, true);

        return $user;
    }

    public function update(object $dto, int $id): ?User
    {
        $user = $this->userRepository->find($id);
        if (!$user) return null;

        $user->setName($dto->name);
        $user->setRoles($dto->roles);

        if (!empty($dto->groups)) {
            $user->clearGroups();
            $groups = $this->groupRepository->findBy(['id' => $dto->groups]);
            if ($groups) {
                $user->addGroups($groups);
            }
        }

        $this->persist($user, true);

        return $user;
    }

    public function delete(int $id): ?User
    {
        $user = $this->userRepository->find($id);
        if (!$user) return null;

        $this->remove($user, true);

        return $user;
    }
}