<?php

namespace Marello\Bundle\RuleBundle\Tests\Unit\Entity;

use Marello\Bundle\RuleBundle\Entity\Rule;
use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use Oro\Component\Testing\Unit\EntityTrait;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    use EntityTestCaseTrait;
    use EntityTrait;

    public function testProperties()
    {
        $now = new \DateTime();
        $properties = [
            ['id', '123'],
            ['name', 'Test Rule'],
            ['enabled', true],
            ['sortOrder', 10],
            ['stopProcessing', true],
            ['system', true],
            ['createdAt', $now, false],
            ['updatedAt', $now, false],
        ];

        $rule = new Rule();
        static::assertPropertyAccessors($rule, $properties);
    }
}
