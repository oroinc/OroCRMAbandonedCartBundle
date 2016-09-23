<?php

namespace Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use Oro\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

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
