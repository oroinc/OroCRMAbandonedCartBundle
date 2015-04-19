<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Twig;

use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignAbandonedCartRelationManager;

class CampaignExtension extends \Twig_Extension
{
    const NAME = 'orocrm_abandonedcart_campaign';

    /**
     * @var CampaignAbandonedCartRelationManager
     */
    protected $campaignAbandonedCartRelationManager;

    /**
     * @param CampaignAbandonedCartRelationManager $campaignAbandonedCartRelationManager
     */
    public function __construct(CampaignAbandonedCartRelationManager $campaignAbandonedCartRelationManager)
    {
        $this->campaignAbandonedCartRelationManager = $campaignAbandonedCartRelationManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'get_abandonedcart_campaign',
                [$this, 'getAbandonedCartCampaign']
            )
        ];
    }

    /**
     * @param MarketingList $marketingList
     * @return \OroCRM\Bundle\CampaignBundle\Entity\Campaign
     */
    public function getAbandonedCartCampaign(MarketingList $marketingList)
    {
        return $this->campaignAbandonedCartRelationManager
            ->getCampaignByMarketingList($marketingList);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
