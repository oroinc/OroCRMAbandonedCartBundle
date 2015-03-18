<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Oro\Bundle\QueryDesignerBundle\Form\Type\AbstractQueryDesignerType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->add('entity', 'hidden', array('data' => 'OroCRM\Bundle\MagentoBundle\Entity\Cart'))
            ->add('type', 'orocrm_abandonedcart_list_marketing_list_type_hidden');

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
                'data_class' => 'OroCRM\Bundle\MarketingListBundle\Entity\MarketingList',
                'intention' => 'marketing_list',
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
