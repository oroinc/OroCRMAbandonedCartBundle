<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use Doctrine\Common\Persistence\ManagerRegistry;

use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking\TrackingStatProviderFactory;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\MailChimpBundle\Entity\StaticSegment;

class AbandonedCartConversionManager
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var CampaignAbandonedCartRelationManager
     */
    protected $campaignAbandonedCartRelationManager;

    /**
     * @var TrackingStatProviderFactory
     */
    protected $statProviderFactory;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param CampaignAbandonedCartRelationManager $campaignAbandonedCartRelationManager
     * @param TrackingStatProviderFactory $statProviderFactory
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        CampaignAbandonedCartRelationManager $campaignAbandonedCartRelationManager,
        TrackingStatProviderFactory $statProviderFactory
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->campaignAbandonedCartRelationManager = $campaignAbandonedCartRelationManager;
        $this->statProviderFactory = $statProviderFactory;
    }

    /**
     * @param MarketingList $marketingList
     * @return AbandonedCartConversion
     */
    public function findConversionByMarketingList(MarketingList $marketingList)
    {
        $conversionRepository = $this->managerRegistry
            ->getRepository('OroCRMAbandonedCartBundle:AbandonedCartConversion');

        return $conversionRepository->findOneBy(['marketingList' => $marketingList->getId()]);
    }

    /**
     * @param AbandonedCartConversion $conversion
     * @return Tracking\StatResult
     */
    public function findAbandonedCartRelatedStatistic(AbandonedCartConversion $conversion)
    {
        $marketingList = $conversion->getMarketingList();
        $campaign = $this->campaignAbandonedCartRelationManager->getCampaignByMarketingList($marketingList);

        $orderAssociationName = ExtendHelper::buildAssociationName(
            'OroCRM\Bundle\MagentoBundle\Entity\Order',
            'association'
        );

        $campaignAssociationName = ExtendHelper::buildAssociationName(
            'OroCRM\Bundle\CampaignBundle\Entity\Campaign',
            'association'
        );

        $trackingStatProvider = $this->statProviderFactory->create(
            $orderAssociationName,
            $campaignAssociationName
        );

        $statResult = $trackingStatProvider->getStatResult($campaign);

        return $statResult;
    }

    /**
     * @param AbandonedCartConversion $conversion
     * @return StaticSegment|null
     */
    public function findStaticSegment(AbandonedCartConversion $conversion)
    {
        $marketingList = $conversion->getMarketingList();
        return $this->managerRegistry
            ->getRepository('OroCRMMailChimpBundle:StaticSegment')
            ->findOneBy(['marketingList' => $marketingList]);
    }
}
