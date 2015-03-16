<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Twig;

use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignAbandonedCartRelationManager;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class CampaignExtension extends \Twig_Extension
{
    const NAME = 'orocrm_abandonedcart_list_campaign';

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
        return array(
            new \Twig_SimpleFunction(
                'get_abandoned_cart_related_campaign',
                array($this, 'getAbandonedCartRelatedCampaign')
            )
        );
    }

    /**
     * @param MarketingList $marketingList
     * @return \OroCRM\Bundle\CampaignBundle\Entity\Campaign
     */
    public function getAbandonedCartRelatedCampaign(MarketingList $marketingList)
    {
        return
            $this->campaignAbandonedCartRelationManager->getCampaignByMarketingList($marketingList);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}