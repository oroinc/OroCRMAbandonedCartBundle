<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Twig;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartStatistics;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartWorkflow;
use OroCRM\Bundle\AbandonedCartBundle\Twig\ConversionExtension;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartConversionManager;

class ConversionExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConversionExtension
     */
    private $conversionExtension;

    /**
     * @var AbandonedCartConversionManager
     */
    private $conversionManager;

    protected function setUp()
    {
        $this->conversionManager = $this
            ->getMockBuilder('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartConversionManager')
            ->disableOriginalConstructor()->getMock();
        $this->conversionExtension = new ConversionExtension($this->conversionManager);
    }

    public function testName()
    {
        $this->assertEquals('orocrm_abandonedcart_conversion', $this->conversionExtension->getName());
    }

    public function testGetWorkflowRelatedStatistic()
    {
        $workflow = new AbandonedCartWorkflow();
        $statistic = new AbandonedCartStatistics();

        $this->conversionManager
            ->expects($this->once())
            ->method('findWorkflowRelatedStatistic')
            ->will($this->returnValue($statistic));

        $this->conversionExtension->getWorkflowRelatedStatistic($workflow);
    }
}