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
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @param MarketingList $marketingList
     * @return Campaign|null
     */
    public function getCampaignByMarketingList(MarketingList $marketingList)
    {
        $campaignAbandonedCartRelation = $this->getCampaignAbandonedCartRelationRepo()
            ->findOneBy(array('marketingList' => $marketingList->getId()));
        if ($campaignAbandonedCartRelation) {
            return $campaignAbandonedCartRelation->getCampaign();
        } else {
            return null;
        }
    }

    /**
     * @return EntityRepository
     */
    protected function getCampaignAbandonedCartRelationRepo()
    {
        return $this->managerRegistry->getRepository('OroCRMAbandonedCartBundle:CampaignAbandonedCartRelation');
    }
}
