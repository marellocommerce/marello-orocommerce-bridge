<?php

namespace Marello\Bundle\OroCommerceBundle\EventListener\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Marello\Bundle\OroCommerceBundle\Entity\OroCommerceSettings;
use Marello\Bundle\OroCommerceBundle\ImportExport\Reader\TaxRuleExportReader;
use Marello\Bundle\OroCommerceBundle\ImportExport\Writer\AbstractExportWriter;
use Marello\Bundle\OroCommerceBundle\ImportExport\Writer\TaxRuleExportBulkDeleteWriter;
use Marello\Bundle\OroCommerceBundle\ImportExport\Writer\TaxRuleExportCreateWriter;
use Marello\Bundle\OroCommerceBundle\Integration\Connector\OroCommerceTaxRuleConnector;
use Marello\Bundle\OroCommerceBundle\Integration\OroCommerceChannelType;
use Marello\Bundle\TaxBundle\Entity\TaxRule;
use Oro\Bundle\ImportExportBundle\Context\Context;
use Oro\Bundle\IntegrationBundle\Async\Topics;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Component\MessageQueue\Client\Message;
use Oro\Component\MessageQueue\Client\MessagePriority;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;

class ReverseSyncAllTaxRulesListener
{
    /**
     * @var MessageProducerInterface
     */
    protected $producer;

    /**
     * @var TaxRuleExportBulkDeleteWriter
     */
    protected $taxRulesBulkDeleteWriter;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param MessageProducerInterface $producer
     * @param TaxRuleExportBulkDeleteWriter $taxRulesBulkDeleteWriter
     */
    public function __construct(MessageProducerInterface $producer, TaxRuleExportBulkDeleteWriter $taxRulesBulkDeleteWriter)
    {
        $this->producer = $producer;
        $this->taxRulesBulkDeleteWriter = $taxRulesBulkDeleteWriter;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $channel = $args->getEntity();
        if ($channel instanceof Channel && $channel->getType() === OroCommerceChannelType::TYPE && $channel->isEnabled()) {
            $this->entityManager = $args->getEntityManager();
            $taxRules = $this->getAllTaxRules();
            $ids = array_map(function(TaxRule $taxRule) {return $taxRule->getId();}, $taxRules);
            $this->producer->send(
                sprintf('%s.orocommerce', Topics::REVERS_SYNC_INTEGRATION),
                new Message(
                    [
                        'integration_id'       => $channel->getId(),
                        'connector_parameters' => [
                            AbstractExportWriter::ACTION_FIELD => AbstractExportWriter::CREATE_ACTION,
                            TaxRuleExportReader::ID_FILTER => $ids,
                        ],
                        'connector'            => OroCommerceTaxRuleConnector::TYPE,
                        'transport_batch_size' => 100,
                    ],
                    MessagePriority::NORMAL
                )
            );
        }
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $channel = $args->getEntity();
        if ($channel instanceof Channel && $channel->getType() === OroCommerceChannelType::TYPE) {
            $this->entityManager = $args->getEntityManager();
            /** @var OroCommerceSettings $transport */
            $transport = $this->entityManager
                ->getRepository(OroCommerceSettings::class)
                ->find($channel->getTransport()->getId());
            $settingsBag = $transport->getSettingsBag();
            $changeSet = $args->getEntityChangeSet();
            $channelId = $channel->getId();
            if (count($changeSet) > 0 && isset($changeSet['enabled'])) {
                if ($changeSet['enabled'][1] === true) {
                    if ($settingsBag->get(OroCommerceSettings::DELETE_REMOTE_DATA_ON_DEACTIVATION) === true) {
                        $taxRules = $this->getAllTaxRules();
                        foreach ($taxRules as $taxRule) {
                            $data = $taxRule->getData();
                            if (!isset($data[TaxRuleExportCreateWriter::TAX_RULE_ID]) ||
                                !isset($data[TaxRuleExportCreateWriter::TAX_RULE_ID][$channelId]) ||
                                $data[TaxRuleExportCreateWriter::TAX_RULE_ID][$channelId] === null
                            ) {
                                $this->producer->send(
                                    sprintf('%s.orocommerce', Topics::REVERS_SYNC_INTEGRATION),
                                    new Message(
                                        [
                                            'integration_id'       => $channel->getId(),
                                            'connector_parameters' => [
                                                AbstractExportWriter::ACTION_FIELD => AbstractExportWriter::CREATE_ACTION,
                                                TaxRuleExportReader::TAXCODE_FILTER => $taxRule->getTaxCode()->getCode(),
                                                TaxRuleExportReader::TAXRATE_FILTER => $taxRule->getTaxRate()->getCode(),
                                                TaxRuleExportReader::TAXJURISDICTION_FILTER => $taxRule->getTaxJurisdiction()->getCode(),
                                            ],
                                            'connector'            => OroCommerceTaxRuleConnector::TYPE,
                                            'transport_batch_size' => 100,
                                        ],
                                        MessagePriority::NORMAL
                                    )
                                );
                            }
                        }
                    }
                } elseif ($changeSet['enabled'][1] === false &&
                    $settingsBag->get(OroCommerceSettings::DELETE_REMOTE_DATA_ON_DEACTIVATION) === true)
                {
                    $taxRules = $this->getSynchronizedTaxRules();
                    $context = new Context(['channel' => $channelId]);
                    $this->taxRulesBulkDeleteWriter->setImportExportContext($context);
                    $this->taxRulesBulkDeleteWriter->write($taxRules);
                }
            }
        }
    }
    
    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $channel = $args->getEntity();
        if ($channel instanceof Channel && $channel->getType() === OroCommerceChannelType::TYPE) {
            $settingsBag = $channel->getTransport()->getSettingsBag();
            if ($settingsBag->get(OroCommerceSettings::DELETE_REMOTE_DATA_ON_DELETION) === true) {
                $this->entityManager = $args->getEntityManager();
                $taxRules = $this->getSynchronizedTaxRules();
                $context = new Context(['channel' => $channel->getId()]);
                $this->taxRulesBulkDeleteWriter->setImportExportContext($context);
                $this->taxRulesBulkDeleteWriter->write($taxRules);
            }
        }
    }
    
    /**
     * @return TaxRule[]
     */
    private function getAllTaxRules()
    {
        return $this->entityManager->getRepository(TaxRule::class)->findAll();
    }

    /**
     * @return TaxRule[]
     */
    private function getSynchronizedTaxRules()
    {
        return $this->entityManager
            ->getRepository(TaxRule::class)
            ->findByDataKey(TaxRuleExportCreateWriter::TAX_RULE_ID);
    }
}