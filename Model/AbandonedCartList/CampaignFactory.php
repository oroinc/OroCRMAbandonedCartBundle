<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use OroCRM\Bundle\CampaignBundle\Entity\Campaign;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class CampaignFactory
{
    const CAMPAIGN_CODE_POSTFIX = '_code';
    const CAMPAIGN_NAME_POSTFIX = '_name';

    public function create(MarketingList $marketingList)
    {
        $campaign = new Campaign();
        $campaign->setCode($marketingList->getName() . self::CAMPAIGN_CODE_POSTFIX);
        $campaign->setName($marketingList->getName() . self::CAMPAIGN_NAME_POSTFIX);

        return $campaign;
    }
}
