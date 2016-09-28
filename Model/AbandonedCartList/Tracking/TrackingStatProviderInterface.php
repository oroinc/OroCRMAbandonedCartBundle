<?php

namespace Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking;

use Oro\Bundle\CampaignBundle\Entity\Campaign;

interface TrackingStatProviderInterface
{
    /**
     * @param Campaign $campaign
     * @return StatResult
     */
    public function getStatResult(Campaign $campaign);
}
