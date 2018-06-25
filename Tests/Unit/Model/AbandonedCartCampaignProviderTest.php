<?php

namespace Oro\Bundle\AbandonedCartBundle\Tests\Unit\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Oro\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProvider;
use Oro\Bundle\MarketingListBundle\Entity\MarketingList;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AbandonedCartCampaignProviderTest extends \PHPUnit\Framework\TestCase
{
    const ABANDONED_CART_CAMPAIGN_CLASS_NAME = 'AbandonedCartClassName';

    /**
     * @var AbandonedCartCampaignProvider
     */
    protected $provider;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|EntityManager
     */
    protected $manager;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|EntityRepository
     */
    protected $repository;

    protected function setUp()
    {
        /**
         * @var \PHPUnit\Framework\MockObject\MockObject|RegistryInterface $registry
         */
        $registry = $this->createMock('Symfony\Bridge\Doctrine\RegistryInterface');

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

        $this->provider = new AbandonedCartCampaignProvider($registry, self::ABANDONED_CART_CAMPAIGN_CLASS_NAME);
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
            ->with(self::ABANDONED_CART_CAMPAIGN_CLASS_NAME)
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
