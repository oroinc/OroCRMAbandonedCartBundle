<?php

namespace Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use Oro\Bundle\CampaignBundle\Entity\Campaign;
use Oro\Bundle\MarketingListBundle\Entity\MarketingList;

class CampaignFactory
{
    /**
     * @param MarketingList $marketingList
     * @return Campaign
     */
    public function create(MarketingList $marketingList)
    {
        $campaign = new Campaign();

        // Strip All Non-Alpha Numeric Characters and Spaces
        $code = preg_replace("/[^a-z0-9]/i", "_", $marketingList->getName());
        $code = substr($code, 0, 20) . $marketingList->getId();

        $campaign->setCode($code);
        $campaign->setName($marketingList->getName());

        return $campaign;
    }
}
