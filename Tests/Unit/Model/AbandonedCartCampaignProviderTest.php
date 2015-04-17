<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProvider;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

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

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    protected function setUp()
    {
        $registry = $this
            ->getMockBuilder('Symfony\Bridge\Doctrine\RegistryInterface')
            ->getMock();

        $this->repository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
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

    protected function tearDown()
    {
        unset($this->provider);
        unset($this->repository);
        unset($this->manager);
    }

    public function testGetAbandonedCartCampaign()
    {
        $expected = new AbandonedCartCampaign();

        $marketingList = new MarketingList();

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with('OroCRMAbandonedCartBundle:AbandonedCartCampaign')
            ->will($this->returnValue($this->repository));

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['marketingList' => $marketingList])
            ->will($this->returnValue($expected));

        $actual = $this->provider->getAbandonedCartCampaign($marketingList);

        $this->assertNotNull($actual);
        $this->assertEquals($expected, $actual);
    }
}
