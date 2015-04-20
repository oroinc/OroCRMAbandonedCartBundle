<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList;

use Doctrine\Common\Persistence\ObjectManager;

use OroCRM\Bundle\MarketingListBundle\Entity\MarketingListType;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\PredefinedMarketingListFactory;

class PredefinedMarketingListFactoryTest extends \PHPUnit_Framework_TestCase
{
    const MARKETING_LIST_TYPE_CLASS_NAME = 'OroCRM\Bundle\MarketingListBundle\Entity\MarketingListType';

    /**
     * @var PredefinedMarketingListFactory
     */
    protected $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $cartClassName;

    protected function setUp()
    {
        $this->cartClassName = 'CartClassName';
        $this->objectManager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->getMock();
        $this->factory = new PredefinedMarketingListFactory(
            $this->objectManager,
            $this->cartClassName,
            self::MARKETING_LIST_TYPE_CLASS_NAME
        );
    }

    public function testCreate()
    {
        $marketingListType = new MarketingListType(MarketingListType::TYPE_DYNAMIC);

        $this->objectManager->expects($this->once())->method('find')
            ->with(self::MARKETING_LIST_TYPE_CLASS_NAME, MarketingListType::TYPE_DYNAMIC)
            ->will($this->returnValue($marketingListType));

        $marketingList = $this->factory->create();

        $this->assertInstanceOf('OroCRM\Bundle\MarketingListBundle\Entity\MarketingList', $marketingList);
        $this->assertEquals($marketingListType, $marketingList->getType());
        $this->assertEquals($this->cartClassName, $marketingList->getEntity());
    }
}
