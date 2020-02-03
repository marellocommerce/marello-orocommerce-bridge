<?php

namespace Marello\Bundle\ServicePointBundle\Constraint;

use Symfony\Component\Validator\Constraint;

class BusinessHoursOverrides extends Constraint
{
    public $closedMessage = 'marello.servicepoint.business_hours_overrides.closed.message';
    public $openMessage = 'marello.servicepoint.business_hours_overrides.open.message';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
