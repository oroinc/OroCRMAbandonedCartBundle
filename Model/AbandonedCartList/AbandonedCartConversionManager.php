<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartWorkflow;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class AbandonedCartConversionManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param MarketingList $marketingList
     * @return \OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion
     */
    public function findConversionByMarketingList(MarketingList $marketingList)
    {
        $conversionRepository = $this->em->getRepository('OroCRMAbandonedCartBundle:AbandonedCartConversion');
        return $conversionRepository->findOneBy(array('marketingList' => $marketingList->getId()));
    }

    /**
     * @param AbandonedCartWorkflow $workflow
     * @return \OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartStatistics
     */
    public function findWorkflowRelatedStatistic(AbandonedCartWorkflow $workflow)
    {
        $statisticRepository = $this->em->getRepository('OroCRMAbandonedCartBundle:AbandonedCartStatistics');
        return $statisticRepository->findOneBy(array('workflow' => $workflow->getId()));
    }
}