<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Type;

use OroCRM\Bundle\MarketingListBundle\Form\Type\MarketingListType;
use Symfony\Component\Form\FormBuilderInterface;

class AbandonedCartListType extends MarketingListType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orocrm_abandonedcart_list';
    }
}
