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
     * @param ManagerRegistry $managerRegistry
     * @param CampaignAbandonedCartRelationManager $campaignAbandonedCartRelationManager
     * @param TrackingStatProviderFactory $statProviderFactory
     * @param string $magentoOrderClassName
     * @param string $campaignClassName
     * @param string $staticSegmentClassName
     * @param string $abandonedCartConversionClassName
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        CampaignAbandonedCartRelationManager $campaignAbandonedCartRelationManager,
        TrackingStatProviderFactory $statProviderFactory,
        $magentoOrderClassName,
        $campaignClassName,
        $staticSegmentClassName,
        $abandonedCartConversionClassName
    ) {
        $this->assertClassNames(
            $magentoOrderClassName,
            $campaignClassName,
            $staticSegmentClassName,
            $abandonedCartConversionClassName
        );

        $this->managerRegistry = $managerRegistry;
        $this->campaignAbandonedCartRelationManager = $campaignAbandonedCartRelationManager;
        $this->statProviderFactory = $statProviderFactory;
        $this->magentoOrderClassName = $magentoOrderClassName;
        $this->campaignClassName = $campaignClassName;
        $this->staticSegmentClassName = $staticSegmentClassName;
        $this->abandonedCartConversionClassName = $abandonedCartConversionClassName;
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
     * @param AbandonedCartConversion $conversion
     * @return Tracking\StatResult
     */
    public function findAbandonedCartRelatedStatistic(AbandonedCartConversion $conversion)
    {
        $marketingList = $conversion->getMarketingList();
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
            ->getRepository($this->staticSegmentClassName)
            ->findOneBy(['marketingList' => $marketingList]);
    }

    /**
     * @param string $magentoOrderClassName
     * @param string $campaignClassName
     * @param string $staticSegmentClassName
     * @param string $abandonedCartConversionClassName
     * @throws \InvalidArgumentException
     */
    protected function assertClassNames(
        $magentoOrderClassName,
        $campaignClassName,
        $staticSegmentClassName,
        $abandonedCartConversionClassName
    ) {
        if (!is_string($magentoOrderClassName) || empty($magentoOrderClassName)) {
            throw new \InvalidArgumentException('Magento Order class name should be provided.');
        }
        if (!is_string($campaignClassName) || empty($campaignClassName)) {
            throw new \InvalidArgumentException('MarketingList class name should be provided.');
        }
        if (!is_string($staticSegmentClassName) || empty($staticSegmentClassName)) {
            throw new \InvalidArgumentException('StaticSegment class name should be provided.');
        }
        if (!is_string($abandonedCartConversionClassName) || empty($abandonedCartConversionClassName)) {
            throw new \InvalidArgumentException('AbandonedCartConversion class name should be provided.');
        }
    }
}
