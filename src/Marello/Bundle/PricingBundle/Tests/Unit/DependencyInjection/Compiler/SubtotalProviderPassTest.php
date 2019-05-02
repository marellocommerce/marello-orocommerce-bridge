<?php

namespace Marello\Bundle\PricingBundle\Tests\Unit\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use Marello\Bundle\PricingBundle\DependencyInjection\Compiler\SubtotalProviderPass;

class SubtotalProviderPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SubtotalProviderPass
     */
    protected $compilerPass;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder
     */
    protected $container;

    protected function setUp()
    {
        $this->container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->getMock();

        $this->compilerPass = new SubtotalProviderPass();
    }

    protected function tearDown()
    {
        unset($this->container, $this->compilerPass);
    }

    public function testServiceNotExists()
    {
        $this->container->expects($this->once())
            ->method('hasDefinition')
            ->with($this->equalTo(SubtotalProviderPass::COMPOSITE_SERVICE))
            ->will($this->returnValue(false));

        $this->container->expects($this->never())
            ->method('getDefinition');

        $this->container->expects($this->never())
            ->method('findTaggedServiceIds');

        $this->compilerPass->process($this->container);
    }

    public function testServiceExistsNotTaggedServices()
    {
        $this->container->expects($this->once())
            ->method('hasDefinition')
            ->with($this->equalTo(SubtotalProviderPass::COMPOSITE_SERVICE))
            ->will($this->returnValue(true));

        $this->container->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo(SubtotalProviderPass::TAG))
            ->will($this->returnValue([]));

        $this->container->expects($this->never())
            ->method('getDefinition');

        $this->compilerPass->process($this->container);
    }

    public function testServiceExistsWithTaggedServices()
    {
        $this->container->expects($this->once())
            ->method('hasDefinition')
            ->with($this->equalTo(SubtotalProviderPass::COMPOSITE_SERVICE))
            ->will($this->returnValue(true));

        $definition = $this->createMock(Definition::class);

        $this->container->expects($this->once())
            ->method('getDefinition')
            ->with($this->equalTo(SubtotalProviderPass::COMPOSITE_SERVICE))
            ->will($this->returnValue($definition));

        $taggedServices = [
            'service.name.1' => [['priority' => 1]],
            'service.name.2' => [[]],
            'service.name.3' => [['priority' => -255]],
            'service.name.4' => [['priority' => 255]],
        ];

        $definition
            ->expects($this->exactly(4))
            ->method('addMethodCall')
            ->withConsecutive(
                ['addProvider', [new Reference('service.name.3')]],
                ['addProvider', [new Reference('service.name.2')]],
                ['addProvider', [new Reference('service.name.1')]],
                ['addProvider', [new Reference('service.name.4')]]
            );

        $this->container->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo(SubtotalProviderPass::TAG))
            ->will($this->returnValue($taggedServices));

        $this->compilerPass->process($this->container);
    }
}
