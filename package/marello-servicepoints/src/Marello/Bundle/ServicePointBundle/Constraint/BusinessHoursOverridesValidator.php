<?php

namespace Marello\Bundle\ServicePointBundle\Constraint;

use Marello\Bundle\ServicePointBundle\Entity\BusinessHoursOverride;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BusinessHoursOverridesValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof BusinessHoursOverride) {
            throw new UnexpectedTypeException($value, BusinessHoursOverride::class);
        }

        if ($value->getOpenStatus() === BusinessHoursOverride::STATUS_CLOSED) {
            if (count($value->getTimePeriods()) > 0) {
                $this->context
                    ->buildViolation($constraint->closedMessage)
                    ->atPath('openStatus')
                    ->addViolation()
                ;
            }
        } else {
            if (count($value->getTimePeriods()) === 0) {
                $this->context
                    ->buildViolation($constraint->openMessage)
                    ->atPath('openStatus')
                    ->addViolation()
                ;
            }
        }
    }
}
