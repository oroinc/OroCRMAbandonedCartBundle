<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\CampaignBundle\Entity\Campaign;

class CampaignAbandonedCartRelationManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
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
        return $this->em->getRepository('OroCRMAbandonedCartBundle:CampaignAbandonedCartRelation');
    }
}
