<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList;

use OroCRM\Bundle\CampaignBundle\Entity\Campaign;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartCampaignFactory;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignFactory;

class AbandonedCartCampaignFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartCampaignFactory
     */
    protected $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CampaignFactory
     */
    protected $campaignFactory;

    protected function setUp()
    {
        $this->campaignFactory = $this
            ->getMockBuilder('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignFactory')
            ->getMock();
        $this->factory = new AbandonedCartCampaignFactory($this->campaignFactory);
    }

    public function testCreate()
    {
        $campaign      = new Campaign();
        $marketingList = new MarketingList();

        $this->campaignFactory->expects($this->once())->method('create')
            ->with($marketingList)->will($this->returnValue($campaign));

        $abandonedCartCampaign = $this->factory->create($marketingList);

        $this->assertInstanceOf(
            'OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign',
            $abandonedCartCampaign
        );

        $this->assertEquals($campaign, $abandonedCartCampaign->getCampaign());
        $this->assertEquals($marketingList, $abandonedCartCampaign->getMarketingList());
    }
}
