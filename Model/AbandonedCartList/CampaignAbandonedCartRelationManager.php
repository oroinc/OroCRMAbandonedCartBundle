<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\CampaignBundle\Entity\Campaign;

class CampaignAbandonedCartRelationManager
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var string
     */
    protected $abandonedCartCampaignClassName;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param $abandonedCartCampaignClassName
     */
    public function __construct(ManagerRegistry $managerRegistry, $abandonedCartCampaignClassName)
    {
        $this->managerRegistry = $managerRegistry;
        $this->abandonedCartCampaignClassName = $abandonedCartCampaignClassName;
    }

    /**
     * @param MarketingList $marketingList
     * @return Campaign|null
     */
    public function getCampaignByMarketingList(MarketingList $marketingList)
    {
        $campaignAbandonedCartRelation = $this->getCampaignAbandonedCartRelationRepo()
            ->findOneBy(['marketingList' => $marketingList->getId()]);

        if ($campaignAbandonedCartRelation) {
            return $campaignAbandonedCartRelation->getCampaign();
        }

        return null;
    }

    /**
     * @return EntityRepository
     */
    protected function getCampaignAbandonedCartRelationRepo()
    {
        return $this->managerRegistry->getRepository($this->abandonedCartCampaignClassName);
    }
}
