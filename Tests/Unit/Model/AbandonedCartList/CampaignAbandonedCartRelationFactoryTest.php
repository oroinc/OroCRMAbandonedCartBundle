<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList;

use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignAbandonedCartRelationFactory;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\CampaignBundle\Entity\Campaign;

class CampaignAbandonedCartRelationFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CampaignAbandonedCartRelationFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new CampaignAbandonedCartRelationFactory();
    }

    public function testCreate()
    {
        $campaign      = new Campaign();
        $marketingList = new MarketingList();

        $campaignAbandonedCartRelation = $this->factory->create($campaign, $marketingList);

        $this->assertInstanceOf(
            'OroCRM\Bundle\AbandonedCartBundle\Entity\CampaignAbandonedCartRelation',
            $campaignAbandonedCartRelation
        );

        $this->assertEquals($campaign, $campaignAbandonedCartRelation->getCampaign());
        $this->assertEquals($marketingList, $campaignAbandonedCartRelation->getMarketingList());
    }
}
