<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SegmentDefinitionHelper
{
    const REQUEST_DEFINITION_KEY = 'definition';

    /**
     * Extract segment definition from Request object
     *
     * @param FormInterface $form
     * @param Request $request
     * @return null|string
     */
    public function extractFromRequest(FormInterface $form, Request $request)
    {
        $formName = $form->getName();
        $data = $request->get($formName);
        if (false === isset($data[self::REQUEST_DEFINITION_KEY])) {
            return null;
        }
        return $data[self::REQUEST_DEFINITION_KEY];
    }
}
