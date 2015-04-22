<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Placeholder;

use Doctrine\Common\Persistence\ManagerRegistry;

use OroCRM\Bundle\MailChimpBundle\Entity\Campaign;
use OroCRM\Bundle\MailChimpBundle\Entity\StaticSegment;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;

class CampaignsPlaceholderFilter
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
        $staticSegment = $this->getStaticSegment($entity);
        $mailchimpCampaign = $this->getMailchimpCampaign($staticSegment);

        if ($this->abandonedCartCampaignProvider->getAbandonedCartCampaign($entity) && $mailchimpCampaign) {
            return true;
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
     * @param StaticSegment $staticSegment
     * @return Campaign|null
     */
    protected function getMailchimpCampaign(StaticSegment $staticSegment)
    {
        $mailchimpCampaign = $this->managerRegistry
            ->getRepository($this->mailchimpCampaignClassName)
            ->findOneBy(['staticSegment' => $staticSegment]);

        return $mailchimpCampaign;
    }
}
