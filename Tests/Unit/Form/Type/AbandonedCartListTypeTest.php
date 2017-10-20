<?php

namespace Oro\Bundle\AbandonedCartBundle\Tests\Unit\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Oro\Bundle\AbandonedCartBundle\Form\Type\AbandonedCartListType;

class AbandonedCartListTypeTest extends \PHPUnit_Framework_TestCase
{
    const CART_CLASS_NAME = 'Oro\Bundle\MagentoBundle\Entity\Cart';
    const MARKETING_LIST_TYPE_CLASS_NAME = 'Oro\Bundle\MarketingListBundle\Form\Type\MarketingListType';
    const MARKETING_LIST_CLASS_NAME = 'Oro\Bundle\MarketingListBundle\Entity\MarketingList';

    /**
     * @var AbandonedCartListType
     */
    protected $abandonedCartListType;

    protected function setUp()
    {
        $this->abandonedCartListType = new AbandonedCartListType(
            self::CART_CLASS_NAME,
            self::MARKETING_LIST_TYPE_CLASS_NAME,
            self::MARKETING_LIST_CLASS_NAME
        );
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
                'oro_resizeable_rich_text',
                ['required' => false]
            )
            ->will($this->returnSelf());

        $builder->expects($this->at(2))
            ->method('add')
            ->with(
                'entity',
                'hidden',
                ['data' => self::CART_CLASS_NAME]
            )
            ->will($this->returnSelf());

        /**
         * @var \PHPUnit_Framework_MockObject_MockObject|FormBuilder $builder
         */
        $builder->expects($this->at(5))
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
        /**
         * @var \PHPUnit_Framework_MockObject_MockObject|OptionsResolverInterface $resolver
         */
        $resolver = $this->createMock('Symfony\Component\OptionsResolver\OptionsResolverInterface');
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with(
                [
                    'column_column_field_choice_options' => [
                        'exclude_fields' => ['relation_type'],
                    ],
                    'column_column_choice_type'   => 'hidden',
                    'filter_column_choice_type'   => 'oro_entity_field_select',
                    'data_class'                  => self::MARKETING_LIST_CLASS_NAME,
                    'intention'                   => 'marketing_list',
                    'cascade_validation'          => true
                ]
            );

        $this->abandonedCartListType->setDefaultOptions($resolver);
    }

    public function testGetName()
    {
        $this->assertEquals('oro_abandonedcart_list', $this->abandonedCartListType->getName());
    }
}
