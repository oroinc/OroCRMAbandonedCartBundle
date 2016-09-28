<?php

namespace Oro\Bundle\AbandonedCartBundle\EventListener\ImportExport;

use Oro\Bundle\ImportExportBundle\Event\StrategyEvent;
use Oro\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;
use Oro\Bundle\AbandonedCartBundle\Model\ExtendedMergeVar\CampaignCodeMergeVarProvider;
use Oro\Bundle\MailChimpBundle\Entity\ExtendedMergeVar;
use Oro\Bundle\MailChimpBundle\Entity\MemberExtendedMergeVar;

class CampaignCodeMergeVarStrategyListener
{
    /**
     * @var AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    /**
     * @param AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider
     */
    public function __construct(AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider)
    {
        $this->abandonedCartCampaignProvider = $abandonedCartCampaignProvider;
    }

    /**
     * @param StrategyEvent $event
     * @return void
     */
    public function onProcessAfter(StrategyEvent $event)
    {
        $entity = $event->getEntity();

        if (!($entity instanceof MemberExtendedMergeVar)) {
            return;
        }

        $staticSegment = $entity->getStaticSegment();

        if (!$staticSegment) {
            return;
        }

        $campaignCodeMergeVar = $staticSegment->getExtendedMergeVars()
            ->filter(
                function (ExtendedMergeVar $extendedMergeVar) {
                    return $extendedMergeVar->getName() === CampaignCodeMergeVarProvider::CAMPAIGN_CODE_NAME;
                }
            )->first();

        if (!$campaignCodeMergeVar) {
            return null;
        }

        /** @var AbandonedCartCampaign $campaignToAbandonedCartRelation */
        $abandonedCartCampaign = $this->abandonedCartCampaignProvider
            ->getAbandonedCartCampaign($staticSegment->getMarketingList());

        $entity->addMergeVarValue($campaignCodeMergeVar->getTag(), $abandonedCartCampaign->getCampaign()->getCode());
    }
}
