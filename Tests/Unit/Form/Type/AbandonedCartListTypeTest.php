<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Form\Type;

use OroCRM\Bundle\AbandonedCartBundle\Form\Type\AbandonedCartListType;

class AbandonedCartListTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartListType
     */
    protected $abandonedCartListType;

    protected function setUp()
    {
        $this->abandonedCartListType = new AbandonedCartListType();
    }

    public function testBuildForm()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $builder->expects($this->at(0))
            ->method('add')
            ->with(
                'name',
                'text',
                ['required' => true]
            )
            ->will($this->returnSelf());

        $builder->expects($this->at(1))
            ->method('add')
            ->with(
                'description',
                'textarea',
                ['required' => false]
            )
            ->will($this->returnSelf());

        $builder->expects($this->at(2))
            ->method('add')
            ->with(
                'entity',
                'hidden',
                array('data' => 'OroCRM\Bundle\MagentoBundle\Entity\Cart')
            )
            ->will($this->returnSelf());

        $builder->expects($this->at(3))
            ->method('add')
            ->with(
                'type',
                'orocrm_abandonedcart_list_marketing_list_type_hidden'
            )
            ->will($this->returnSelf());

        $builder->expects($this->at(4))
            ->method('add')
            ->with(
                'definition',
                'hidden',
                ['required' => false]
            )
            ->will($this->returnSelf());

        $this->abandonedCartListType->buildForm($builder, []);
    }

    public function testSetDefaultOptions()
    {
        $resolver = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolverInterface');
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with(
                [
                    'column_column_choice_type'   => 'hidden',
                    'filter_column_choice_type'   => 'oro_entity_field_select',
                    'data_class'                  => 'OroCRM\Bundle\MarketingListBundle\Entity\MarketingList',
                    'intention'                   => 'marketing_list',
                    'cascade_validation'          => true
                ]
            );

        $this->abandonedCartListType->setDefaultOptions($resolver);
    }

    public function testGetName()
    {
        $this->assertEquals('orocrm_abandonedcart_list', $this->abandonedCartListType->getName());
    }
}