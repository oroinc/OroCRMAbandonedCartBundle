<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Doctrine\Common\Persistence\ManagerRegistry;

use OroCRM\Bundle\AbandonedCartBundle\Form\DataTransformer\MarketingListTypeToStringTransformer;

class AbandonedCartHiddenMarketingListTypeType extends AbstractType
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $typeTransformer = new MarketingListTypeToStringTransformer($this->managerRegistry);
        
        $builder
            ->addModelTransformer($typeTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'The selected Marketing List Type does not exist',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orocrm_abandonedcart_list_marketing_list_type_hidden';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'hidden';
    }
}
