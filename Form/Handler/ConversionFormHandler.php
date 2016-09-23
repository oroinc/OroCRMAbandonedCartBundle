<?php

namespace Oro\Bundle\AbandonedCartBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion;

class ConversionFormHandler
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @param FormInterface $form
     * @param Request       $request
     * @param ObjectManager $manager
     */
    public function __construct(FormInterface $form, Request $request, ObjectManager $manager)
    {
        $this->form    = $form;
        $this->request = $request;
        $this->manager = $manager;
    }

    /**
     * Process form
     *
     * @param AbandonedCartConversion $conversion
     *
     * @return bool True on successful processing, false otherwise
     */
    public function process(AbandonedCartConversion $conversion)
    {
        $this->form->setData($conversion);

        if (in_array($this->request->getMethod(), ['POST', 'PUT'])) {
            $this->form->submit($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess($conversion);
                return true;
            }
        }

        return false;
    }

    /**
     * "Success" form handler
     *
     * @param AbandonedCartConversion $conversion
     */
    protected function onSuccess(AbandonedCartConversion $conversion)
    {
        $this->manager->persist($conversion);
        $this->manager->flush();
    }
}
