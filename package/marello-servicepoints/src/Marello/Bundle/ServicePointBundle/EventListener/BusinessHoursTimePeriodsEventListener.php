<?php

namespace Marello\Bundle\ServicePointBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Marello\Bundle\ServicePointBundle\Entity\BusinessHoursOverride;

class BusinessHoursTimePeriodsEventListener implements EventSubscriber
{
    /** @var EntityManagerInterface */
    protected $em;

    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::prePersist,
        ];
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $this->processEvent($event);
    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        $this->processEvent($event);
    }

    protected function processEvent(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof BusinessHoursOverride) {
            $this->em = $event->getEntityManager();

            $this->processEntity($entity);
        }
    }

    protected function processEntity(BusinessHoursOverride $entity): void
    {
        if ($entity->getOpenStatus() === BusinessHoursOverride::STATUS_CLOSED
            && count($entity->getTimePeriods()) > 0
        ) {
            foreach ($entity->getTimePeriods() as $timePeriod) {
                $this->em->remove($timePeriod);
                $entity->removeTimePeriod($timePeriod);
            }

            $this->em->getUnitOfWork()->recomputeSingleEntityChangeSet(
                $this->em->getClassMetadata(BusinessHoursOverride::class),
                $entity
            );
        }
    }
}
