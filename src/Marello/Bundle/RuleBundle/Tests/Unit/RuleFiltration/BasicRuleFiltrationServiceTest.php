<?php

namespace Marello\Bundle\RuleBundle\Tests\Unit\RuleFiltration;

use Marello\Bundle\RuleBundle\Entity\RuleOwnerInterface;
use Marello\Bundle\RuleBundle\RuleFiltration\BasicRuleFiltrationService;

class BasicRuleFiltrationServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BasicRuleFiltrationService
     */
    private $service;

    protected function setUp()
    {
        $this->service = new BasicRuleFiltrationService();
    }

    public function testGetFilteredRuleOwners()
    {
        $context = [];

        $ruleOwners = [
            $this->createPartialMock(RuleOwnerInterface::class, ['getRule']),
            $this->createPartialMock(RuleOwnerInterface::class, ['getRule']),
        ];

        static::assertEquals($ruleOwners, $this->service->getFilteredRuleOwners($ruleOwners, $context));
    }
}
