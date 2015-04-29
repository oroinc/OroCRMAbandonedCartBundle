<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking;

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TrackingStatProviderFactory
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->em = $registry->getManager();
    }

    /**
     * @param $orderAssociationName
     * @param $campaignAssociationName
     * @param $trackingVisitEventClassName
     * @return TrackingStatProvider
     */
    public function create($orderAssociationName, $campaignAssociationName, $trackingVisitEventClassName)
    {
        $statProvider = new TrackingStatProvider(
            $this->em,
            $orderAssociationName,
            $campaignAssociationName,
            $trackingVisitEventClassName
        );
        return $statProvider;
    }
}
