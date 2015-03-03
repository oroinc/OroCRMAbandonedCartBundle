<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList;

use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\SegmentBundle\Entity\SegmentType;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\DynamicTypeSegmentBuilder;

class DynamicTypeSegmentBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DynamicTypeSegmentBuilder
     */
    private $builder;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->getMock();
        $this->builder = new DynamicTypeSegmentBuilder($this->objectManager);
    }

    public function testBuild()
    {
        $dynamicSegmentType = new SegmentType(SegmentType::TYPE_DYNAMIC);
        $this->objectManager->expects($this->once())->method('find')
            ->with('OroSegmentBundle:SegmentType', SegmentType::TYPE_DYNAMIC)
            ->will($this->returnValue($dynamicSegmentType));

        $segment = $this->builder->build();

        $this->assertNotNull($segment);
        $this->assertInstanceOf('Oro\Bundle\SegmentBundle\Entity\Segment', $segment);
        $this->assertEquals(AbandonedCartList::ENTITY_FULL_NAME, $segment->getEntity());
        $this->assertEquals($dynamicSegmentType, $segment->getType());
    }
}
