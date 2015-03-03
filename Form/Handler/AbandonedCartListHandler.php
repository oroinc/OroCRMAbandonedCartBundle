<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartList;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AbandonedCartListHandler
{
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
     * @param FormInterface $form
     * @param Request $request
     * @param ObjectManager $objectManager
     */
    public function __construct(FormInterface $form, Request $request, ObjectManager $objectManager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->objectManager = $objectManager;
    }

    /**
     * Process form handling and saving of the entity
     *
     * @param AbandonedCartList $entity
     * @return bool True for success processing, False otherwise
     */
    public function process(AbandonedCartList $entity)
    {
        $this->form->setData($entity);
        if ($this->request->isMethod('POST') || $this->request->isMethod('PUT')) {
            $this->form->submit($this->request);

            if ($this->form->isValid()) {
                $this->objectManager->persist($entity);
                $this->objectManager->flush();
                return true;
            }
        }
        return false;
    }
}
