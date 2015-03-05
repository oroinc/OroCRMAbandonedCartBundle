<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList;

use Doctrine\Common\Persistence\ObjectManager;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\PredefinedMarketingListFactory;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingListType;

class PredefinedMarketingListFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PredefinedMarketingListFactory
     */
    private $factory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->getMock();
        $this->factory = new PredefinedMarketingListFactory($this->objectManager);
    }

    public function testBuild()
    {
        $marketingListType = new MarketingListType(MarketingListType::TYPE_DYNAMIC);

        $this->objectManager->expects($this->once())->method('find')
            ->with('OroCRMMarketingListBundle:MarketingListType', MarketingListType::TYPE_DYNAMIC)
            ->will($this->returnValue($marketingListType));

        $marketingList = $this->factory->create();

        $this->assertInstanceOf('OroCRM\Bundle\MarketingListBundle\Entity\MarketingList', $marketingList);
        $this->assertEquals($marketingListType, $marketingList->getType());
    }
}