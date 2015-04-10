<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Twig;

use OroCRM\Bundle\AbandonedCartBundle\Twig\CampaignExtension;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignAbandonedCartRelationManager;
use OroCRM\Bundle\CampaignBundle\Entity\Campaign;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class CampaignExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CampaignExtension
     */
    private $campaignExtension;

    /**
     * @var CampaignAbandonedCartRelationManager
     */
    private $campaignAbandonedCartRelationManager;

    protected function setUp()
    {
        $this->campaignAbandonedCartRelationManager = $this
            ->getMockBuilder(
                'OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignAbandonedCartRelationManager'
            )
            ->disableOriginalConstructor()->getMock();
        $this->campaignExtension = new CampaignExtension($this->campaignAbandonedCartRelationManager);
    }

    public function testName()
    {
        $this->assertEquals('orocrm_abandonedcart_list_campaign', $this->campaignExtension->getName());
    }

    public function testGetAbandonedCartRelatedCampaign()
    {
        $campaign = new Campaign();
        $marketingList = new MarketingList();

        $this->campaignAbandonedCartRelationManager
            ->expects($this->once())
            ->method('getCampaignByMarketingList')
            ->will($this->returnValue($campaign));

        $this->campaignExtension->getAbandonedCartRelatedCampaign($marketingList);
    }
}
