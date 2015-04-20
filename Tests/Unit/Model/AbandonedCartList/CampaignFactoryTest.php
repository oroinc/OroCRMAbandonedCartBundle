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
        $marketingList->setName('name  fg');

        $campaign = $this->factory->create($marketingList);

        $this->assertInstanceOf('OroCRM\Bundle\CampaignBundle\Entity\Campaign', $campaign);

        $code = preg_replace("/[^a-z0-9]/i", "_", $marketingList->getName());
        $code = substr($code, 0, 20) . $marketingList->getId();

        $this->assertEquals($campaign->getCode(), $code);
        $this->assertEquals($campaign->getName(), $marketingList->getName());
    }
}
