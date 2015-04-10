<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\MarketingList;

use OroCRM\Bundle\MarketingListBundle\Model\MarketingListSourceInterface;

class AbandonedCartSource implements MarketingListSourceInterface
{
    const SOURCE_CODE = 'abandoned_cart';

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return self::SOURCE_CODE;
    }
}
