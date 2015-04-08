<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Form\Type;

use OroCRM\Bundle\AbandonedCartBundle\Form\Type\AbandonedCartConversionType;

class AbandonedCartConversionTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartConversionType
     */
    protected $abandonedCartConversionType;

    protected function setUp()
    {
        $this->abandonedCartConversionType = new AbandonedCartConversionType();
    }

    public function testBuildForm()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $builder->expects($this->once())
            ->method('add')
            ->with(
                'workflows',
                'entity',
                [
                    'class' => 'OroCRMAbandonedCartBundle:AbandonedCartWorkflow',
                    'multiple' => true,
                    'expanded' => true,
                    'property' => 'name'
                ]
            )
            ->will($this->returnSelf());

        $this->abandonedCartConversionType->buildForm($builder, []);
    }

    public function testSetDefaultOptions()
    {
        $resolver = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolverInterface');
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with(
                ['data_class' => 'OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion']
            );

        $this->abandonedCartConversionType->setDefaultOptions($resolver);
    }

    public function testGetName()
    {
        $this->assertEquals('orocrm_mailchimp_abandonedcart_list_conversion', $this->abandonedCartConversionType->getName());
    }
}