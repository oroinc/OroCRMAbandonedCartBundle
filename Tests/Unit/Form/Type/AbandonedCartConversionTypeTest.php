<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Form\Type;

use OroCRM\Bundle\AbandonedCartBundle\Form\Type\AbandonedCartConversionType;

class AbandonedCartConversionTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartConversionType
     */
    protected $abandonedCartConversionType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $conversionManager;

    protected function setUp()
    {
        $this->conversionManager = $this->getMockBuilder(
                'OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartConversionManager'
            )
            ->disableOriginalConstructor()
            ->getMock();

        $this->abandonedCartConversionType = new AbandonedCartConversionType($this->conversionManager);
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
        $this->assertEquals('orocrm_abandonedcart_conversion', $this->abandonedCartConversionType->getName());
    }
}
