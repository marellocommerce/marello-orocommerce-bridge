<?php

namespace Marello\Bundle\TaxBundle\DependencyInjection\Compiler;

use Marello\Bundle\TaxBundle\Event\ResolverEventConnector;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ResolverEventConnectorPass implements CompilerPassInterface
{
    const TAG_NAME = 'marello_tax.resolver';
    const CONNECTOR_SERVICE_NAME_SUFFIX = 'event.resolver_event_connector';

    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($taggedServices as $id => $tags) {
            if (!$tags) {
                continue;
            }

            $definition = new Definition(ResolverEventConnector::class, [new Reference($id)]);
            foreach ($tags as $tag) {
                if (empty($tag['event'])) {
                    throw new \InvalidArgumentException(sprintf('Wrong tags configuration "%s"', json_encode($tags)));
                }

                $attributes = ['event' => $tag['event'], 'method' => 'onResolve'];
                if (!empty($tag['priority'])) {
                    $attributes['priority'] = $tag['priority'];
                }
                $definition->addTag('kernel.event_listener', $attributes);
            }

            $container->setDefinition(sprintf('%s.%s', $id, self::CONNECTOR_SERVICE_NAME_SUFFIX), $definition);
        }
    }
}
