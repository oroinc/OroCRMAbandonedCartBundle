<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProvider;

class AbandonedCartCampaignProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartCampaignProvider
     */
    protected $provider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    protected function setUp()
    {
        $registry = $this
            ->getMockBuilder('Symfony\Bridge\Doctrine\RegistryInterface')
            ->getMock();

        $this->manager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $registry->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($this->manager));

        $this->provider = new AbandonedCartCampaignProvider($registry);
    }

    public function testGetAbandonedCartCampaign()
    {
        $expected = new AbandonedCartCampaign();

        $marketingListId = 1;
        $marketingList = $this
            ->getMockBuilder('OroCRM\Bundle\MarketingListBundle\Entity\MarketingList')
            ->getMock();

        $marketingList->expects($this->once())->method('getId')
            ->will($this->returnValue($marketingListId));

        $this->manager->expects($this->once())
            ->method('find')
            ->with('OroCRMMarketingListBundle:MarketingList', $marketingListId)
            ->will($this->returnValue($expected));

        $actual = $this->provider->getAbandonedCartCampaign($marketingList);

        $this->assertNotNull($actual);
        $this->assertEquals($expected, $actual);
    }
}
