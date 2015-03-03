<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartListManager;
use OroCRM\Bundle\AbandonedCartBundle\Model\SegmentDefinitionHelper;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AbandonedCartListHandler
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
    protected $objectManager;

    /**
     * @var AbandonedCartListManager
     */
    protected $abandonedCartListManager;

    /**
     * @var SegmentDefinitionHelper
     */
    protected $segmentDefinitionHelper;

    /**
     * @param FormInterface $form
     * @param Request $request
     * @param ObjectManager $objectManager
     * @param AbandonedCartListManager $abandonedCartListManager
     * @param SegmentDefinitionHelper $segmentDefinitionHelper
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        ObjectManager $objectManager,
        AbandonedCartListManager $abandonedCartListManager,
        SegmentDefinitionHelper $segmentDefinitionHelper
    ) {
        $this->form = $form;
        $this->request = $request;
        $this->objectManager = $objectManager;
        $this->abandonedCartListManager = $abandonedCartListManager;
        $this->segmentDefinitionHelper = $segmentDefinitionHelper;
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

            $definition = $this->segmentDefinitionHelper->extractFromRequest($this->form, $this->request);
            if ($definition) {
                $this->abandonedCartListManager->updateSegment($entity, $definition);
            }

            if ($this->form->isValid()) {
                $this->objectManager->persist($entity);
                $this->objectManager->flush();
                return true;
            }
        }
        return false;
    }
}
