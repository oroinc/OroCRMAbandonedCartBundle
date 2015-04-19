<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Form\DataTransformer;

use OroCRM\Bundle\AbandonedCartBundle\Form\DataTransformer\MarketingListTypeToStringTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingListType;

use Symfony\Component\Form\Exception\TransformationFailedException;

class MarketingListTypeToStringTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MarketingListTypeToStringTransformer
     */
    private $marketingListTypeToStringTransformer;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var ObjectRepository
     */
    private $repository;

    protected function setUp()
    {
        $this->om = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->marketingListTypeToStringTransformer = new MarketingListTypeToStringTransformer($this->om);
    }

    public function testTransform()
    {
        $this->assertEquals(
            MarketingListType::TYPE_DYNAMIC, $this->marketingListTypeToStringTransformer->transform(null)
        );
    }

    public function testReverseTransformWhenTypeExists()
    {
        $value = 'dynamic';
        $marketingListType = new MarketingListType(MarketingListType::TYPE_DYNAMIC);

        $this->om->expects($this->once())
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

        $this->om->expects($this->once())
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
