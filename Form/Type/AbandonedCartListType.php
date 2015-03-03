<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Oro\Bundle\QueryDesignerBundle\Form\Type\AbstractQueryDesignerType;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartList;

class AbandonedCartListType extends AbstractQueryDesignerType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', ['required' => true])
            ->add('description', 'textarea', ['required' => false])

            ->add('entity', 'orocrm_abandonedcart_list_entity_choice',
                ['data' => 'OroCRM\Bundle\MagentoBundle\Entity\Cart', 'disabled' => true]
            );


        parent::buildForm($builder, $options);
    }

    /**
     * Gets the default options for this type.
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return [
            'column_column_choice_type' => 'hidden',
            'filter_column_choice_type' => 'oro_entity_field_select'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $options = array_merge(
            $this->getDefaultOptions(),
            [
                'data_class' => 'OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartList',
                'intention' => 'abandonedcart_list',
                'cascade_validation' => true
            ]
        );

        $resolver->setDefaults($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orocrm_abandonedcart_list';
    }
}
