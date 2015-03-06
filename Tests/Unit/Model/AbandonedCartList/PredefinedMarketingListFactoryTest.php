<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList;

use Doctrine\Common\Persistence\ObjectManager;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\PredefinedMarketingListFactory;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingListType;
use OroCRM\Bundle\MarketingListBundle\Model\MarketingListSourceInterface;

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

    /**
     * @var MarketingListSourceInterface
     */
    private $source;

    protected function setUp()
    {
        $this->objectManager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->getMock();
        $this->source = $this->getMockBuilder('OroCRM\Bundle\MarketingListBundle\Model\MarketingListSourceInterface')
            ->getMock();
        $this->factory = new PredefinedMarketingListFactory($this->objectManager, $this->source);
    }

    public function testCreate()
    {
        $marketingListType = new MarketingListType(MarketingListType::TYPE_DYNAMIC);

        $this->objectManager->expects($this->once())->method('find')
            ->with('OroCRMMarketingListBundle:MarketingListType', MarketingListType::TYPE_DYNAMIC)
            ->will($this->returnValue($marketingListType));

        $this->source->expects($this->once())->method('getCode')->will($this->returnValue('source_code'));

        $marketingList = $this->factory->create();

        $this->assertInstanceOf('OroCRM\Bundle\MarketingListBundle\Entity\MarketingList', $marketingList);
        $this->assertEquals($marketingListType, $marketingList->getType());
        $this->assertEquals('source_code', $marketingList->getSource());
    }
}
