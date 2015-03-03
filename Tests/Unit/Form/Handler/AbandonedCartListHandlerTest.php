<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartList;
use OroCRM\Bundle\AbandonedCartBundle\Form\Handler\AbandonedCartListHandler;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartListManager;
use OroCRM\Bundle\AbandonedCartBundle\Model\SegmentDefinitionHelper;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AbandonedCartListHandlerTest extends \PHPUnit_Framework_TestCase
{
    const SEGMENT_DEFINITION = 'SEGMENT_DEFINITION';

    /**
     * @var AbandonedCartListHandler
     */
    private $handler;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var AbandonedCartListManager
     */
    private $abandonedCartListManager;

    /**
     * @var SegmentDefinitionHelper
     */
    private $segmentDefinitionHelper;

    /**
     * @var AbandonedCartList
     */
    private $entity;

    protected function setUp()
    {
        $this->form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')->getMock();
        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $this->objectManager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->getMock();
        $this->abandonedCartListManager = $this
            ->getMockBuilder(
                'OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartListManager'
            )->disableOriginalConstructor()->getMock();
        $this->segmentDefinitionHelper = $this
            ->getMockBuilder(
                'OroCRM\Bundle\AbandonedCartBundle\Model\SegmentDefinitionHelper'
            )->getMock();
        $this->handler = new AbandonedCartListHandler(
            $this->form, $this->request, $this->objectManager,
            $this->abandonedCartListManager, $this->segmentDefinitionHelper
        );

        $this->entity = new AbandonedCartList();
        $this->form->expects($this->once())->method('setData')->with($this->entity);
    }

    public function testProcessWhenRequestMethodIsWrong()
    {
        $this->request->expects($this->at(0))->method('isMethod')->with('POST')->will($this->returnValue(false));
        $this->request->expects($this->at(1))->method('isMethod')->with('PUT')->will($this->returnValue(false));
        $this->form->expects($this->never())->method('submit');
        $this->form->expects($this->never())->method('isValid');
        $this->objectManager->expects($this->never())->method('persist');
        $this->objectManager->expects($this->never())->method('flush');

        $result = $this->handler->process($this->entity);

        $this->assertFalse($result);
    }

    public function testProcessWhenFormIsInvalid()
    {
        $this->request->expects($this->once())->method('isMethod')->with('POST')->will($this->returnValue(true));
        $this->form->expects($this->once())->method('submit')->with($this->request);
        $this->segmentDefinitionHelper->expects($this->once())->method('extractFromRequest')
            ->with($this->form, $this->request)->will($this->returnValue(self::SEGMENT_DEFINITION));
        $this->form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $this->objectManager->expects($this->never())->method('persist');
        $this->objectManager->expects($this->never())->method('flush');

        $result = $this->handler->process($this->entity);

        $this->assertFalse($result);
    }

    public function testProcessWhenSegmentDefinitionDoesNotExist()
    {
        $this->request->expects($this->once())->method('isMethod')->with('POST')->will($this->returnValue(true));
        $this->form->expects($this->once())->method('submit')->with($this->request);
        $this->segmentDefinitionHelper->expects($this->once())->method('extractFromRequest')
            ->with($this->form, $this->request)->will($this->returnValue(null));
        $this->abandonedCartListManager->expects($this->never())->method('updateSegment');
        $this->form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $this->objectManager->expects($this->once())->method('persist')->with($this->entity);
        $this->objectManager->expects($this->once())->method('flush');

        $result = $this->handler->process($this->entity);

        $this->assertTrue($result);
    }

    public function testProcess()
    {
        $this->request->expects($this->once())->method('isMethod')->with('POST')->will($this->returnValue(true));
        $this->form->expects($this->once())->method('submit')->with($this->request);
        $this->segmentDefinitionHelper->expects($this->once())->method('extractFromRequest')
            ->with($this->form, $this->request)->will($this->returnValue(self::SEGMENT_DEFINITION));
        $this->abandonedCartListManager->expects($this->once())->method('updateSegment')
            ->with($this->entity, self::SEGMENT_DEFINITION);
        $this->form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $this->objectManager->expects($this->once())->method('persist')->with($this->entity);
        $this->objectManager->expects($this->once())->method('flush');

        $result = $this->handler->process($this->entity);

        $this->assertTrue($result);
    }
}
