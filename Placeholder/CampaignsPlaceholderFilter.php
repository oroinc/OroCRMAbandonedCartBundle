<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Placeholder;

use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;

class CampaignsPlaceholderFilter
{
    /**
     * @var AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    /**
     * @param AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider
     */
    public function __construct(AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider)
    {
        $this->abandonedCartCampaignProvider = $abandonedCartCampaignProvider;
    }

    /**
     * @param MarketingList $entity
     * @return bool
     */
    public function isApplicable($entity)
    {
        if ($this->abandonedCartCampaignProvider->getAbandonedCartCampaign($entity)) {
            return true;
        } else {
            return false;
        }
    }
}
