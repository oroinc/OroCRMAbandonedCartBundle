<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking;

use Doctrine\ORM\EntityManager;

class TrackingStatProviderFactory
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param $orderAssociationName
     * @param $campaignAssociationName
     * @return TrackingStatProvider
     */
    public function create($orderAssociationName, $campaignAssociationName)
    {
        $statProvider = new TrackingStatProvider($this->em, $orderAssociationName, $campaignAssociationName);
        return $statProvider;
    }
}
