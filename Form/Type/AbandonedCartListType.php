<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Type;

use OroCRM\Bundle\MarketingListBundle\Form\Type\MarketingListType;

class AbandonedCartListType extends MarketingListType
{
//    /**
//     * {@inheritdoc}
//     */
//    public function buildForm(FormBuilderInterface $builder, array $options)
//    {
//        $builder
//            ->add('name', 'text', ['required' => true])
//            ->add('description', 'textarea', ['required' => false])
//
//            ->add('entity', 'orocrm_abandonedcart_list_entity_choice',
//                ['data' => 'OroCRM\Bundle\MagentoBundle\Entity\Cart', 'disabled' => true]
//            );
//
//
//        parent::buildForm($builder, $options);
//    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orocrm_abandonedcart_list';
    }
}
