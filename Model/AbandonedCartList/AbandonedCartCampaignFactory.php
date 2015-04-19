<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class AbandonedCartCampaignFactory
{
    /**
     * @var CampaignFactory
     */
    protected $campaignFactory;

    /**
     * @param CampaignFactory $campaignFactory
     */
    public function __construct(CampaignFactory $campaignFactory)
    {
        $this->campaignFactory = $campaignFactory;
    }

    /**
     * @param MarketingList $marketingList
     * @return AbandonedCartCampaign
     */
    public function create(MarketingList $marketingList)
    {
        $campaign = $this->campaignFactory->create($marketingList);
        $abandonedCartCampaign = new AbandonedCartCampaign();
        $abandonedCartCampaign->setMarketingList($marketingList);
        $abandonedCartCampaign->setCampaign($campaign);
        return $abandonedCartCampaign;
    }
}
