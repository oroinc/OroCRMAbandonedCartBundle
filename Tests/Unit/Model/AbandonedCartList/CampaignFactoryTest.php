<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList;

use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignFactory;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class CampaignFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CampaignFactory
     */
    protected $factory;

    protected function setUp()
    {
        $this->factory = new CampaignFactory();
    }

    public function testCreate()
    {
        $marketingList = new MarketingList();
        $marketingList->setName('name');

        $campaign = $this->factory->create($marketingList);

        $this->assertInstanceOf('OroCRM\Bundle\CampaignBundle\Entity\Campaign', $campaign);
        $this->assertEquals($campaign->getCode(), $marketingList->getName() . CampaignFactory::CAMPAIGN_CODE_POSTFIX);
        $this->assertEquals($campaign->getName(), $marketingList->getName() . CampaignFactory::CAMPAIGN_NAME_POSTFIX);
    }
}
