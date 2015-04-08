<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AbandonedCartConversionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'workflows',
                'entity',
                array(
                    'class' => 'OroCRMAbandonedCartBundle:AbandonedCartWorkflow',
                    'multiple' => true,
                    'expanded' => true,
                    'property' => 'name'
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orocrm_mailchimp_abandonedcart_list_conversion';
    }
}