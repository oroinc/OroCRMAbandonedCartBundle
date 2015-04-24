<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use Doctrine\Common\Persistence\ManagerRegistry;

use OroCRM\Bundle\MailChimpBundle\Entity\StaticSegment;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;

class AbandonedCartRelatedCampaignsManager
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    /**
     * @var string
     */
    protected $staticSegmentClassName;

    /**
     * @var string
     */
    protected $mailchimpCampaignClassName;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider
     * @param string $staticSegmentClassName
     * @param string $mailchimpCampaignClassName
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider,
        $staticSegmentClassName,
        $mailchimpCampaignClassName
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->abandonedCartCampaignProvider = $abandonedCartCampaignProvider;
        $this->staticSegmentClassName = $staticSegmentClassName;
        $this->mailchimpCampaignClassName = $mailchimpCampaignClassName;
    }

    /**
     * @param MarketingList $entity
     * @return bool
     */
    public function isApplicable($entity)
    {
        if ($this->abandonedCartCampaignProvider->getAbandonedCartCampaign($entity)) {
            return $this->isMailchimpCampaign($entity);
        }

        return false;
    }

    /**
     * @param MarketingList $marketingList
     * @return StaticSegment|null
     */
    protected function getStaticSegment(MarketingList $marketingList)
    {
        $staticSegment = $this->managerRegistry
            ->getRepository($this->staticSegmentClassName)
            ->findOneBy(['marketingList' => $marketingList]);

        return $staticSegment;
    }

    /**
     * @param MarketingList $marketingList
     * @return bool
     */
    protected function isMailchimpCampaign(MarketingList $marketingList)
    {
        $staticSegment = $this->managerRegistry
            ->getRepository($this->staticSegmentClassName)
            ->findOneBy(['marketingList' => $marketingList]);

        if (!$staticSegment) {
            return false;
        } else {
            $mailchimpCampaign = $this->managerRegistry
                ->getRepository($this->mailchimpCampaignClassName)
                ->findOneBy(['staticSegment' => $staticSegment]);
        }

        return (bool)$mailchimpCampaign;
    }
}
