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
     * @var string
     */
    protected $abandonedCartCampaignClassName;

    /**
     * @param RegistryInterface $doctrine
     * @param string $abandonedCartCampaignClassName
     */
    public function __construct(RegistryInterface $doctrine, $abandonedCartCampaignClassName)
    {
        $this->manager = $doctrine->getManager();
        $this->abandonedCartCampaignClassName = $abandonedCartCampaignClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function getAbandonedCartCampaign(MarketingList $marketingList)
    {
        $abandonedCartCampaign = $this->manager
            ->getRepository($this->abandonedCartCampaignClassName)
            ->findOneBy(['marketingList' => $marketingList]);
        return $abandonedCartCampaign;
    }
}
