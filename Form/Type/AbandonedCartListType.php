<?php

namespace Oro\Bundle\AbandonedCartBundle\Form\Type;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Oro\Bundle\MarketingListBundle\Entity\MarketingListType;
use Oro\Bundle\QueryDesignerBundle\Form\Type\AbstractQueryDesignerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbandonedCartListType extends AbstractQueryDesignerType
{
    /**
     * @var string
     */
    protected $cartClassName;

    /**
     * @var string
     */
    protected $marketingListTypeClassName;

    /**
     * @var string
     */
    protected $marketingListClassName;

    /**
     * @param string $cartClassName
     * @param string $marketingListTypeClassName
     * @param string $marketingListClassName
     */
    public function __construct($cartClassName, $marketingListTypeClassName, $marketingListClassName)
    {
        $this->cartClassName = $cartClassName;
        $this->marketingListTypeClassName = $marketingListTypeClassName;
        $this->marketingListClassName = $marketingListClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', ['required' => true])
            ->add('description', 'oro_resizeable_rich_text', ['required' => false])
            ->add('entity', 'hidden', ['data' => $this->cartClassName]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();

                $qb = function (EntityRepository $er) {
                    return $er->createQueryBuilder('mlt')
                        ->andWhere('mlt.name = :manualTypeName')
                        ->setParameter('manualTypeName', MarketingListType::TYPE_DYNAMIC)
                        ->addOrderBy('mlt.name', Criteria::ASC);
                };

                $form->add(
                    'type',
                    'entity',
                    [
                        'class' => $this->marketingListTypeClassName,
                        'property' => 'label',
                        'required' => true,
                        'query_builder' => $qb
                    ]
                );
            }
        );
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                if ($data && !$data->getId()) {
                    $data->setSegment(null);
                    $event->setData($data);
                }
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
            'column_column_field_choice_options' => [
                'exclude_fields' => ['relation_type'],
            ],
            'column_column_choice_type' => 'hidden',
            'filter_column_choice_type' => 'oro_entity_field_select'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $options = array_merge(
            $this->getDefaultOptions(),
            [
                'data_class' => $this->marketingListClassName,
                'csrf_token_id' => 'marketing_list',
                'query_type' => 'segment',
            ]
        );

        $resolver->setDefaults($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oro_abandonedcart_list';
    }
}
