<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Twig;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartWorkflow;
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
                'get_workflow_related_statistic',
                array($this, 'getWorkflowRelatedStatistic')
            )
        );
    }

    /**
     * @param AbandonedCartWorkflow $workflow
     * @return \OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartStatistics
     */
    public function getWorkflowRelatedStatistic(AbandonedCartWorkflow $workflow)
    {
        return
            $this->conversionManager->findWorkflowRelatedStatistic($workflow);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}