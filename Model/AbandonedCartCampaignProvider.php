<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model;

use Doctrine\ORM\EntityManager;

use Symfony\Bridge\Doctrine\RegistryInterface;

use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class AbandonedCartCampaignProvider implements AbandonedCartCampaignProviderInterface
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }

    /**
     * {@inheritdoc}
     */
    public function getAbandonedCartCampaign(MarketingList $marketingList)
    {
        $abandonedCartCampaign = $this->manager
            ->find('OroCRMMarketingListBundle:MarketingList', $marketingList->getId());
        return $abandonedCartCampaign;
    }
}
