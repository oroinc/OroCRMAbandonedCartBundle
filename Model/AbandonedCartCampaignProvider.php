<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

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
        $abandonedCartCampaign = $this->getAbandonedCartCampaignRepository()
            ->findOneBy(['marketingList' => $marketingList]);
        return $abandonedCartCampaign;
    }

    /**
     * @return EntityRepository
     */
    protected function getAbandonedCartCampaignRepository()
    {
        return $this->manager->getRepository($this->abandonedCartCampaignClassName);
    }
}
