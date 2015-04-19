<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartConversionManager;

class AbandonedCartConversionType extends AbstractType
{
    /**
     * @var AbandonedCartConversionManager
     */
    protected $conversionManager;

    /**
     * @param AbandonedCartConversionManager $conversionManager
     */
    public function __construct(AbandonedCartConversionManager $conversionManager)
    {
        $this->conversionManager = $conversionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $conversion = $event->getData();
                $form = $event->getForm();
                $qb = null;
                if ($conversion) {
                    $staticSegment = $this->conversionManager->findStaticSegment($conversion);

                    $qb = function (EntityRepository $entityRepository) use ($staticSegment) {
                        return $entityRepository->createQueryBuilder('mc')
                            ->orderBy('mc.title', 'ASC')
                            ->where('mc.staticSegment = :staticSegment')
                            ->setParameter('staticSegment', $staticSegment);
                    };
                }

                $form->add(
                    'campaigns',
                    'entity',
                    [
                        'class' => 'OroCRMMailChimpBundle:Campaign',
                        'query_builder' => $qb,
                        'multiple' => true,
                        'expanded' => true,
                        'property' => 'title'
                    ]
                );
            }
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
        return 'orocrm_abandonedcart_conversion';
    }
}
