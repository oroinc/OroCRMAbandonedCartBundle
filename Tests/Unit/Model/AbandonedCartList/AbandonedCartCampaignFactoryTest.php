<?php

namespace Oro\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList;

use Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartCampaignFactory;
use Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignFactory;
use Oro\Bundle\CampaignBundle\Entity\Campaign;
use Oro\Bundle\MarketingListBundle\Entity\MarketingList;

class AbandonedCartCampaignFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AbandonedCartCampaignFactory
     */
    protected $factory;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|CampaignFactory
     */
    protected $campaignFactory;

    protected function setUp()
    {
        $this->campaignFactory = $this
            ->getMockBuilder('Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignFactory')
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
            'Oro\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign',
            $abandonedCartCampaign
        );

        $this->assertEquals($campaign, $abandonedCartCampaign->getCampaign());
        $this->assertEquals($marketingList, $abandonedCartCampaign->getMarketingList());
    }
}
