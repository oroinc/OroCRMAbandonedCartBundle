<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\TrackingBundle\Entity\TrackingVisitEvent;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignAbandonedCartRelationManager;

class AbandonedCartConversionManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var CampaignAbandonedCartRelationManager
     */
    private $campaignAbandonedCartRelationManager;

    /**
     * @param EntityManager $entityManager
     * @param CampaignAbandonedCartRelationManager $campaignAbandonedCartRelationManager
     */
    public function __construct(
        EntityManager $entityManager,
        CampaignAbandonedCartRelationManager $campaignAbandonedCartRelationManager
    )
    {
        $this->em = $entityManager;
        $this->campaignAbandonedCartRelationManager = $campaignAbandonedCartRelationManager;
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
     * @param AbandonedCartConversion $conversion
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findAbandonedCartRelatedStatistic(AbandonedCartConversion $conversion)
    {
        $marketingList = $conversion->getMarketingList();
        $campaign = $this->campaignAbandonedCartRelationManager->getCampaignByMarketingList($marketingList);

        $qb = $this->em->getRepository('OroTrackingBundle:TrackingVisitEvent')
            ->createQueryBuilder('te');

        $orderAssociationName = ExtendHelper::buildAssociationName(
            'OroCRM\Bundle\MagentoBundle\Entity\Order',
            'association'
        );

        $campaignAssociationName = ExtendHelper::buildAssociationName(
            'OroCRM\Bundle\CampaignBundle\Entity\Campaign',
            'association'
        );

        $result = $qb
            ->select('sum(o.totalAmount) as total, count(o.id) as qty')
            ->join('te.' . $orderAssociationName, 'o')
            ->where('te.' . $campaignAssociationName . '= :campaignId')
            ->setParameter('campaignId', $campaign->getId())
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }
}
