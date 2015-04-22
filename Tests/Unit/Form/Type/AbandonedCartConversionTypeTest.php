<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use OroCRM\Bundle\AbandonedCartBundle\Form\Type\AbandonedCartConversionType;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartConversionManager;

class AbandonedCartConversionTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartConversionType
     */
    protected $abandonedCartConversionType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbandonedCartConversionManager
     */
    protected $conversionManager;

    /**
     * @var string
     */
    protected $mailChimpCampaignClassName;

    protected function setUp()
    {
        $this->conversionManager = $this->getMockBuilder(
            'OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartConversionManager'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->mailChimpCampaignClassName = 'ClassName';
        $this->abandonedCartConversionType = new AbandonedCartConversionType(
            $this->conversionManager,
            $this->mailChimpCampaignClassName
        );
    }

    public function testSetDefaultOptions()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|OptionsResolverInterface $resolver */
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
