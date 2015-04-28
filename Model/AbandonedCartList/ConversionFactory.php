<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion;

class ConversionFactory
{
    /**
     * @param MarketingList $marketingList
     * @return AbandonedCartConversion
     */
    public function create(MarketingList $marketingList)
    {
        $conversion = new AbandonedCartConversion();
        $conversion->setMarketingList($marketingList);

        return $conversion;
    }
}
