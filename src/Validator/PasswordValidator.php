<?php

namespace App\Validator;

use App\Dto\Http\UserDTO;
use App\Dto\UserInput;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PasswordValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManager $entityManager) {}

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Password) {
            throw new UnexpectedTypeException($constraint, Password::class);
        }

        /**
         * @var $userDtoObject UserDTO|UserInput
         */
        $userDtoObject = $this->context->getObject();
        if (!$userDtoObject instanceof UserDTO && !$userDtoObject instanceof UserInput) {
            throw new UnexpectedTypeException(UserDTO::class, get_class($userDtoObject));
        }

        /** @var EntityManagerInterface $entityManager */
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findBy([
            'email' => $userDtoObject instanceof UserDTO ? $userDtoObject->asObject()->email : $userDtoObject->email
        ]);
        if ($user) return;

        if (null === $value || '' === $value) {
            $this->context->buildViolation($constraint->messageEmpty)->addViolation();
        }

        if (strlen($value) < 6 || strlen($value) > 12) {
            $this->context->buildViolation($constraint->messageInvalid)
                ->setParameter('{{ password }}', $value)
                ->addViolation();
        }
    }
}