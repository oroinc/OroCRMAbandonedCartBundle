<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Form\Type;

use OroCRM\Bundle\AbandonedCartBundle\Form\Type\AbandonedCartHiddenMarketingListTypeType;
use Doctrine\Common\Persistence\ObjectManager;

class AbandonedCartHiddenMarketingListTypeTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartHiddenMarketingListTypeType
     */
    protected $abandonedCartHiddenMarketingListTypeType;

    /**
     * @var ObjectManager
     */
    protected $om;

    protected function setUp()
    {
        $this->om = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->abandonedCartHiddenMarketingListTypeType = new AbandonedCartHiddenMarketingListTypeType($this->om);
    }

    public function testBuildForm()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $builder->expects($this->once())
            ->method('addModelTransformer')
            ->will($this->returnSelf());

        $this->abandonedCartHiddenMarketingListTypeType->buildForm($builder, []);
    }

    public function testGetName()
    {
        $this->assertEquals('orocrm_abandonedcart_list_marketing_list_type_hidden', $this->abandonedCartHiddenMarketingListTypeType->getName());
    }

    public function testGetParent()
    {
        $this->assertEquals('hidden', $this->abandonedCartHiddenMarketingListTypeType->getParent());
    }
}