<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

interface AbandonedCartCampaignProviderInterface
{
    /**
     * Retrieves AbandonedCart Campaign
     *
     * @param MarketingList $marketingList
     * @return AbandonedCartCampaign|null
     */
    public function getAbandonedCartCampaign(MarketingList $marketingList);
}
