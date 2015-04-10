<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Doctrine\Common\Persistence\ObjectManager;
use OroCRM\Bundle\AbandonedCartBundle\Form\DataTransformer\MarketingListTypeToStringTransformer;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AbandonedCartHiddenMarketingListTypeType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $typeTransformer = new MarketingListTypeToStringTransformer($this->om);
        
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
