<?php

namespace Oro\Bundle\AbandonedCartBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion;
use Oro\Bundle\FormBundle\Form\Handler\RequestHandlerTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ConversionFormHandler
{
    use RequestHandlerTrait;

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @param FormInterface $form
     * @param RequestStack $requestStack
     * @param ObjectManager $manager
     */
    public function __construct(FormInterface $form, RequestStack $requestStack, ObjectManager $manager)
    {
        $this->form = $form;
        $this->requestStack = $requestStack;
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

        $request = $this->requestStack->getCurrentRequest();
        if (in_array($request->getMethod(), ['POST', 'PUT'], true)) {
            $this->submitPostPutRequest($this->form, $request);

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
