<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Twig;

use OroCRM\Bundle\CampaignBundle\Entity\Campaign;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\AbandonedCartBundle\Twig\CampaignExtension;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignAbandonedCartRelationManager;

class CampaignExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CampaignExtension
     */
    protected $campaignExtension;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CampaignAbandonedCartRelationManager
     */
    protected $campaignAbandonedCartRelationManager;

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
        $this->assertEquals('orocrm_abandonedcart_campaign', $this->campaignExtension->getName());
    }

    public function testGetAbandonedCartRelatedCampaign()
    {
        $campaign = new Campaign();
        $marketingList = new MarketingList();

        $this->campaignAbandonedCartRelationManager
            ->expects($this->once())
            ->method('getCampaignByMarketingList')
            ->will($this->returnValue($campaign));

        $this->campaignExtension->getAbandonedCartCampaign($marketingList);
    }

    public function testGetFunctions()
    {
        $functions = $this->campaignExtension->getFunctions();
        $this->assertCount(1, $functions);

        $expectedFunctions = array(
            'get_abandonedcart_campaign'
        );

        /** @var \Twig_SimpleFunction $function */
        foreach ($functions as $function) {
            $this->assertInstanceOf('\Twig_SimpleFunction', $function);
            $this->assertContains($function->getName(), $expectedFunctions);
        }
    }
}
