<?php

namespace Oro\Bundle\AbandonedCartBundle\Tests\Unit\Form\Type;

use Oro\Bundle\AbandonedCartBundle\Form\Type\AbandonedCartListType;
use Oro\Bundle\EntityBundle\Form\Type\EntityFieldSelectType;
use Oro\Bundle\FormBundle\Form\Type\OroResizeableRichTextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbandonedCartListTypeTest extends \PHPUnit\Framework\TestCase
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
                TextType::class,
                ['required' => true]
            )
            ->will($this->returnSelf());

        $builder->expects($this->at(1))
            ->method('add')
            ->with(
                'description',
                OroResizeableRichTextType::class,
                ['required' => false]
            )
            ->will($this->returnSelf());

        $builder->expects($this->at(2))
            ->method('add')
            ->with(
                'entity',
                HiddenType::class,
                ['data' => self::CART_CLASS_NAME]
            )
            ->will($this->returnSelf());

        /**
         * @var \PHPUnit\Framework\MockObject\MockObject|FormBuilder $builder
         */
        $builder->expects($this->at(5))
            ->method('add')
            ->with(
                'definition',
                HiddenType::class,
                ['required' => false]
            )
            ->will($this->returnSelf());

        $this->abandonedCartListType->buildForm($builder, []);
    }

    public function testConfigureOptions()
    {
        /**
         * @var \PHPUnit\Framework\MockObject\MockObject|OptionsResolver $resolver
         */
        $resolver = $this->createMock('Symfony\Component\OptionsResolver\OptionsResolver');
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with(
                [
                    'column_column_field_choice_options' => [
                        'exclude_fields' => ['relation_type'],
                    ],
                    'column_column_choice_type'   => HiddenType::class,
                    'filter_column_choice_type'   => EntityFieldSelectType::class,
                    'data_class'                  => self::MARKETING_LIST_CLASS_NAME,
                    'csrf_token_id'               => 'marketing_list',
                    'query_type'                  => 'segment',
                ]
            );

        $this->abandonedCartListType->configureOptions($resolver);
    }
}
