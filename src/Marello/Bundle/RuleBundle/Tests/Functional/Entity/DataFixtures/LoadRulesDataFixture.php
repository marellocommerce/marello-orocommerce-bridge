<?php

namespace Marello\Bundle\RuleBundle\Tests\Functional\Entity\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Marello\Bundle\RuleBundle\Entity\Rule;
use Oro\Bundle\TestFrameworkBundle\Test\DataFixtures\AbstractFixture;
use Symfony\Component\Yaml\Yaml;

class LoadRulesDataFixture extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getRulesData() as $reference => $data) {
            $entity = new Rule();
            $entity
                ->setName($reference)
                ->setEnabled((bool)$data['enabled'])
                ->setSortOrder((int)$data['sort_order'])
                ->setSystem($data['is_system'])
                ->setStopProcessing((bool)$data['is_stop_processing']);

            $manager->persist($entity);

            $this->setReference($reference, $entity);
        }

        $manager->flush();
    }

    /**
     * @return array
     */
    protected function getRulesData()
    {
        return Yaml::parse(file_get_contents(__DIR__.'/data/basic_rule.yml'));
    }
}
