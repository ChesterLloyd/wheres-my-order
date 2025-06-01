<?php

namespace App\EventListener;

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
        $entity->setAddedBy($this->security->getUser());
        $entity->setAddedAt(new DateTime());
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
        $entity->setUpdatedBy($this->security->getUser());
        $entity->setUpdatedAt(new DateTime());
    }
}
