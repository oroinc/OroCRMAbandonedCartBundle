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
     * @var string
     */
    protected $magentoOrderClassName;

    /**
     * @var string
     */
    protected $campaignClassName;

    /**
     * @var string
     */
    protected $staticSegmentClassName;

    /**
     * @var string
     */
    protected $abandonedCartConversionClassName;

    /**
     * @var string
     */
    protected $trackingVisitEventClassName;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param CampaignAbandonedCartRelationManager $campaignAbandonedCartRelationManager
     * @param TrackingStatProviderFactory $statProviderFactory
     * @param string $magentoOrderClassName
     * @param string $campaignClassName
     * @param string $staticSegmentClassName
     * @param string $abandonedCartConversionClassName
     * @param string $trackingVisitEventClassName
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        CampaignAbandonedCartRelationManager $campaignAbandonedCartRelationManager,
        TrackingStatProviderFactory $statProviderFactory,
        $magentoOrderClassName,
        $campaignClassName,
        $staticSegmentClassName,
        $abandonedCartConversionClassName,
        $trackingVisitEventClassName
    ) {
        $this->assertClassName($magentoOrderClassName, 'Magento Order');
        $this->assertClassName($campaignClassName, 'Campaign');
        $this->assertClassName($staticSegmentClassName, 'StaticSegment');
        $this->assertClassName($abandonedCartConversionClassName, 'Conversion');
        $this->assertClassName($trackingVisitEventClassName, 'Tracking Visit Event');

        $this->managerRegistry = $managerRegistry;
        $this->campaignAbandonedCartRelationManager = $campaignAbandonedCartRelationManager;
        $this->statProviderFactory = $statProviderFactory;
        $this->magentoOrderClassName = $magentoOrderClassName;
        $this->campaignClassName = $campaignClassName;
        $this->staticSegmentClassName = $staticSegmentClassName;
        $this->abandonedCartConversionClassName = $abandonedCartConversionClassName;
        $this->trackingVisitEventClassName = $trackingVisitEventClassName;
    }

    /**
     * @param MarketingList $marketingList
     * @return AbandonedCartConversion
     */
    public function findConversionByMarketingList(MarketingList $marketingList)
    {
        $conversionRepository = $this->managerRegistry
            ->getRepository($this->abandonedCartConversionClassName);

        return $conversionRepository->findOneBy(['marketingList' => $marketingList->getId()]);
    }

    /**
     * @param MarketingList $marketingList
     * @return Tracking\StatResult
     */
    public function findAbandonedCartRelatedStatistic(MarketingList $marketingList)
    {
        $campaign = $this->campaignAbandonedCartRelationManager->getCampaignByMarketingList($marketingList);

        $orderAssociationName = ExtendHelper::buildAssociationName(
            $this->magentoOrderClassName,
            'association'
        );

        $campaignAssociationName = ExtendHelper::buildAssociationName(
            $this->campaignClassName,
            'association'
        );

        $trackingStatProvider = $this->statProviderFactory->create(
            $orderAssociationName,
            $campaignAssociationName,
            $this->trackingVisitEventClassName
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
            ->getRepository($this->staticSegmentClassName)
            ->findOneBy(['marketingList' => $marketingList]);
    }

    /**
     * @param string $expectedClassName
     * @param string $actualClassName
     * @throws \InvalidArgumentException
     */
    protected function assertClassName($expectedClassName, $actualClassName)
    {
        if (!is_string($actualClassName) || empty($actualClassName)) {
            throw new \InvalidArgumentException($expectedClassName . ' class name should be provided.');
        }
    }
}
