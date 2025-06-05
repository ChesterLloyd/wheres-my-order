<?php

namespace App\EventListener;

use App\Entity\InboundEmail;
use App\Entity\Item;
use App\Entity\Purchase;
use App\Entity\Store;
use App\Entity\User;
use DateTime;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

readonly class AuditUpdater
{
    public function __construct(
        private Security $security,
    )
    {
    }

    /**
     * Update created by and created at fields.
     *
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($this->entityHasAuditFields($entity)) {
            $entity->setAddedBy($this->security->getUser());
            $entity->setAddedAt(new DateTime());
        }
    }

    /**
     * Update updated by and updated at fields.
     *
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($this->entityHasAuditFields($entity)) {
            $entity->setUpdatedBy($this->security->getUser());
            $entity->setUpdatedAt(new DateTime());
        }
    }

    /**
     * Returns true if the entity supports audit fields.
     *
     * @param $entity
     * @return bool
     */
    private function entityHasAuditFields($entity): bool
    {
        return (
            $entity instanceof InboundEmail ||
            $entity instanceof Item ||
            $entity instanceof Purchase ||
            $entity instanceof Store ||
            $entity instanceof User
        );
    }
}
