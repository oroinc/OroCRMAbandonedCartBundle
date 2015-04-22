<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Twig;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion;
use OroCRM\Bundle\AbandonedCartBundle\Twig\ConversionExtension;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartConversionManager;

class ConversionExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConversionExtension
     */
    protected $conversionExtension;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbandonedCartConversionManager
     */
    protected $conversionManager;

    protected function setUp()
    {
        $this->conversionManager = $this
            ->getMockBuilder('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartConversionManager')
            ->disableOriginalConstructor()->getMock();
        $this->conversionExtension = new ConversionExtension($this->conversionManager);
    }

    public function testGetName()
    {
        $this->assertEquals('orocrm_abandonedcart_conversion', $this->conversionExtension->getName());
    }

    public function testGetAbandonedCartRelatedStatistic()
    {
        $conversion = new AbandonedCartConversion();
        $result = [];

        $this->conversionManager
            ->expects($this->once())
            ->method('findAbandonedCartRelatedStatistic')
            ->will($this->returnValue($result));

        $this->conversionExtension->getAbandonedCartRelatedStatistic($conversion);
    }

    public function testGetFunctions()
    {
        $functions = $this->conversionExtension->getFunctions();
        $this->assertCount(1, $functions);

        $expectedFunctions = array(
            'get_abandonedcart_related_statistic'
        );

        /** @var \Twig_SimpleFunction $function */
        foreach ($functions as $function) {
            $this->assertInstanceOf('\Twig_SimpleFunction', $function);
            $this->assertContains($function->getName(), $expectedFunctions);
        }
    }
}
