<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking;

use OroCRM\Bundle\CampaignBundle\Entity\Campaign;

interface TrackingStatProviderInterface
{
    /**
     * @param Campaign $campaign
     * @return StatResult
     */
    public function getStatResult(Campaign $campaign);
}
