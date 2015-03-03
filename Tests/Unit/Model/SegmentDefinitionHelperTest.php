<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model;

use OroCRM\Bundle\AbandonedCartBundle\Model\SegmentDefinitionHelper;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SegmentDefinitionHelperTest extends \PHPUnit_Framework_TestCase
{
    const FORM_NAME = 'FORM_NAME';

    const REQUEST_SEGMENT_DEFINITION = 'REQUEST_SEGMENT_DEFINITION';

    /**
     * @var SegmentDefinitionHelper
     */
    private $helper;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var Request
     */
    private $request;

    protected function setUp()
    {
        $this->form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')->getMock();
        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $this->form->expects($this->once())->method('getName')->will($this->returnValue(self::FORM_NAME));
        $this->helper = new SegmentDefinitionHelper();
    }

    public function testExtractFromRequestWhenDefinitionIsMissing()
    {
        $this->request->expects($this->once())->method('get')
            ->with(self::FORM_NAME)->will($this->returnValue(array()));
        $definition = $this->helper->extractFromRequest($this->form, $this->request);
        $this->assertNull($definition);
    }

    public function testExtractFromRequest()
    {
        $this->request
            ->expects($this->once())->method('get')
            ->with(self::FORM_NAME)
            ->will(
                $this->returnValue(
                    array(SegmentDefinitionHelper::REQUEST_DEFINITION_KEY => self::REQUEST_SEGMENT_DEFINITION)
                )
            );
        $definition = $this->helper->extractFromRequest($this->form, $this->request);
        $this->assertEquals(self::REQUEST_SEGMENT_DEFINITION, $definition);
    }
}
