<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Twig;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartConversionManager;

class ConversionExtension extends \Twig_Extension
{
    const NAME = 'orocrm_abandonedcart_conversion';

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
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'get_abandonedcart_related_statistic',
                array($this, 'getAbandonedCartRelatedStatistic')
            )
        );
    }

    /**
     * @param AbandonedCartConversion $conversion
     * @return mixed
     */
    public function getAbandonedCartRelatedStatistic(AbandonedCartConversion $conversion)
    {
        return
            $this->conversionManager->findAbandonedCartRelatedStatistic($conversion);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
