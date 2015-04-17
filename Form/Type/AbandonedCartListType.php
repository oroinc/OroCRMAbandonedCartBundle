<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Type;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Oro\Bundle\QueryDesignerBundle\Form\Type\AbstractQueryDesignerType;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingListType;

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
            ->add('entity', 'hidden', ['data' => 'OroCRM\Bundle\MagentoBundle\Entity\Cart']);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();

                $qb = function (EntityRepository $er) {
                    return $er->createQueryBuilder('mlt')
                        ->andWhere('mlt.name = :manualTypeName')
                        ->setParameter('manualTypeName', MarketingListType::TYPE_DYNAMIC)
                        ->addOrderBy('mlt.name', 'ASC');
                };

                $form->add(
                    'type',
                    'entity',
                    [
                        'class' => 'OroCRMMarketingListBundle:MarketingListType',
                        'property' => 'label',
                        'required' => true,
                        'query_builder' => $qb
                    ]
                );
            }
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
