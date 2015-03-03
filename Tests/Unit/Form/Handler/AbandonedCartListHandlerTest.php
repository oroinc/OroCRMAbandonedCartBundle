<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartList;
use OroCRM\Bundle\AbandonedCartBundle\Form\Handler\AbandonedCartListHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AbandonedCartListHandlerTest extends \PHPUnit_Framework_TestCase
{
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
     * @var AbandonedCartList
     */
    private $entity;

    protected function setUp()
    {
        $this->form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')->getMock();
        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $this->objectManager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->getMock();
        $this->handler = new AbandonedCartListHandler($this->form, $this->request, $this->objectManager);

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
        $this->form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $this->objectManager->expects($this->never())->method('persist');
        $this->objectManager->expects($this->never())->method('flush');

        $result = $this->handler->process($this->entity);

        $this->assertFalse($result);
    }

    public function testProcess()
    {
        $this->request->expects($this->once())->method('isMethod')->with('POST')->will($this->returnValue(true));
        $this->form->expects($this->once())->method('submit')->with($this->request);
        $this->form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $this->objectManager->expects($this->once())->method('persist')->with($this->entity);
        $this->objectManager->expects($this->once())->method('flush');

        $result = $this->handler->process($this->entity);

        $this->assertTrue($result);
    }
}
