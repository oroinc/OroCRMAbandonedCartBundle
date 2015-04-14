<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Form\DataTransformer;

use OroCRM\Bundle\MarketingListBundle\Entity\MarketingListType;
use OroCRM\Bundle\AbandonedCartBundle\Form\DataTransformer\MarketingListTypeToStringTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;

class MarketingListTypeToStringTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MarketingListTypeToStringTransformer
     */
    protected $marketingListTypeToStringTransformer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $managerRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    protected function setUp()
    {
        $this->managerRegistry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->marketingListTypeToStringTransformer = new MarketingListTypeToStringTransformer($this->managerRegistry);
    }

    public function testTransform()
    {
        $this->assertEquals(
            MarketingListType::TYPE_DYNAMIC,
            $this->marketingListTypeToStringTransformer->transform(null)
        );
    }

    public function testReverseTransformWhenTypeExists()
    {
        $value = 'dynamic';
        $marketingListType = new MarketingListType(MarketingListType::TYPE_DYNAMIC);

        $this->managerRegistry->expects($this->once())
            ->method('getRepository')
            ->with('OroCRMMarketingListBundle:MarketingListType')
            ->will($this->returnValue($this->repository));

        $this->repository
            ->expects($this->once())->method('findOneBy')
            ->with(array('name' => $value))
            ->will($this->returnValue($marketingListType));

        $this->marketingListTypeToStringTransformer->reverseTransform($value);
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function testReverseTransformWhenTypeDoesNotExists()
    {
        $value = 'dynamic';

        $this->managerRegistry->expects($this->once())
            ->method('getRepository')
            ->with('OroCRMMarketingListBundle:MarketingListType')
            ->will($this->returnValue($this->repository));

        $this->repository
            ->expects($this->once())->method('findOneBy')
            ->with(array('name' => $value))
            ->will($this->returnValue(null));

        $this->marketingListTypeToStringTransformer->reverseTransform($value);
    }
}
