<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ContainsRolesValidator extends ConstraintValidator
{
    public function __construct(private readonly array $roles) {}

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ContainsRoles) {
            throw new UnexpectedTypeException($constraint, ContainsRoles::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_array($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'array');
        }

        if (empty($this->roles)) {
            $this->context->buildViolation($constraint->messageRolesMissing)->addViolation();
        }

        if (empty($value)) {
            $this->context->buildViolation($constraint->messageRolesEmpty)->addViolation();
        }

        $values = array_intersect($this->roles, $value);

        if (empty($values)) {
            $this->context->buildViolation($constraint->messageRolesInvalid)
                ->setParameter('{{ roles }}', implode('|', $value))
                ->addViolation();
        }
    }
}