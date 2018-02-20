<?php

namespace Oro\Bundle\AbandonedCartBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartConversionManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbandonedCartConversionType extends AbstractType
{
    /**
     * @var AbandonedCartConversionManager
     */
    protected $conversionManager;

    /**
     * @var string
     */
    protected $mailChimpCampaignClassName;

    /**
     * @param AbandonedCartConversionManager $conversionManager
     * @param string $mailChimpCampaignClassName
     */
    public function __construct(AbandonedCartConversionManager $conversionManager, $mailChimpCampaignClassName)
    {
        if (!is_string($mailChimpCampaignClassName) || empty($mailChimpCampaignClassName)) {
            throw new \InvalidArgumentException('MailChimpCampaign class name should be provided.');
        }

        $this->conversionManager = $conversionManager;
        $this->mailChimpCampaignClassName = $mailChimpCampaignClassName;
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
                        'class' => $this->mailChimpCampaignClassName,
                        'required' => true,
                        'query_builder' => $qb,
                        'multiple' => true,
                        'expanded' => true,
                        'property' => 'title',
                    ]
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => 'Oro\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion']
        );
    }

    /**
     * {@inheritdoc}
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
        return 'oro_abandonedcart_conversion';
    }
}
