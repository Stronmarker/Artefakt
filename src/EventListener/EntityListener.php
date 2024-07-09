<?php

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\Project;

class EntityListener
{
    public function prePersist(Project $project, LifecycleEventArgs $event)
    {
        if ($project->getCreatedAt() === null) {
            $project->setCreatedAt(new \DateTime());
        }
        if ($project->getUpdatedAt() === null) {
            $project->setUpdatedAt(new \DateTime());
        }
    }

    public function preUpdate(Project $project, LifecycleEventArgs $event)
    {
        $project->setUpdatedAt(new \DateTime());
    }
}
