<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use OroCRM\Bundle\AbandonedCartBundle\Entity\CampaignAbandonedCartRelation;
use OroCRM\Bundle\CampaignBundle\Entity\Campaign;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class CampaignAbandonedCartRelationFactory
{
    public function create(Campaign $campaign, MarketingList $marketingList)
    {
        $campaignAbandonedCartRelation = new CampaignAbandonedCartRelation();
        $campaignAbandonedCartRelation->setCampaign($campaign);
        $campaignAbandonedCartRelation->setMarketingList($marketingList);

        return $campaignAbandonedCartRelation;
    }
}
